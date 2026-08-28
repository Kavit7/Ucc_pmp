<?php

use yii\db\Migration;

/**
 * Seeds region/district/ward/street with a full mainland-Tanzania dataset
 * (source: HackEAC/tanzania-locations-db, bundled locally under
 * @app/data/tanzania-locations/*.csv) so property location entry can be a
 * search instead of manual data entry. Existing rows are matched by
 * (case-insensitive) name and reused rather than duplicated. Uses raw
 * batch inserts instead of ActiveRecord - at ~20k rows, per-row
 * ActiveRecord saves (each doing its own MAX(uuid) lookup) would be far
 * too slow, and these models' beforeSave() assumes a web user context
 * that doesn't exist in the console.
 */
class m260828_030000_seed_tanzania_locations extends Migration
{
    public function safeUp()
    {
        $countryId = (new \yii\db\Query())
            ->select('country_id')
            ->from('country')
            ->where(['country_name' => 'Tanzania'])
            ->scalar();

        if (!$countryId) {
            $countryId = (new \yii\db\Query())->from('country')->select('country_id')->scalar();
        }

        // --- Load existing rows so we reuse ids instead of duplicating ---
        $regionMap = [];   // upper(name) => region_id
        foreach ((new \yii\db\Query())->select(['region_id', 'name'])->from('region')->all() as $r) {
            $regionMap[strtoupper(trim($r['name']))] = (int) $r['region_id'];
        }

        $districtMap = []; // region_id|upper(name) => district_id
        foreach ((new \yii\db\Query())->select(['district_id', 'region_id', 'district_name'])->from('district')->all() as $d) {
            $districtMap[$d['region_id'] . '|' . strtoupper(trim($d['district_name']))] = (int) $d['district_id'];
        }

        $wardMap = []; // district_id|upper(name) => ward_id
        foreach ((new \yii\db\Query())->select(['ward_id', 'district_id', 'ward_name'])->from('ward')->all() as $w) {
            $wardMap[$w['district_id'] . '|' . strtoupper(trim($w['ward_name']))] = (int) $w['ward_id'];
        }

        $streetMap = []; // district_id|upper(name) => street_id
        foreach ((new \yii\db\Query())->select(['street_id', 'district_id', 'street_name'])->from('street')->all() as $s) {
            $streetMap[$s['district_id'] . '|' . strtoupper(trim($s['street_name']))] = (int) $s['street_id'];
        }

        $nextRegionUuid = $this->nextUuidNumber('region', 'uuid', 'Reg_');
        $nextDistrictUuid = $this->nextUuidNumber('district', 'uuid', 'Dis_');
        $nextWardUuid = $this->nextUuidNumber('ward', 'uuid', 'Ward_');
        $nextStreetUuid = $this->nextUuidNumber('street', 'uuid', 'Stre_');

        $newRegions = [];
        $newDistricts = [];
        $newWards = [];
        $newStreets = [];

        $now = date('Y-m-d H:i:s');

        $files = glob(Yii::getAlias('@app/data/tanzania-locations/*.csv'));
        sort($files);

        foreach ($files as $file) {
            $fh = fopen($file, 'r');
            if (!$fh) {
                continue;
            }
            fgetcsv($fh); // header

            while (($row = fgetcsv($fh)) !== false) {
                if (count($row) < 7) {
                    continue;
                }
                $regionName = ucwords(strtolower(trim($row[0])));
                $districtName = ucwords(strtolower(trim($row[2])));
                $wardName = ucwords(strtolower(trim($row[4])));
                $streetName = trim($row[6]);

                if ($regionName === '' || $districtName === '') {
                    continue;
                }

                $regionKey = strtoupper($regionName);
                if (!isset($regionMap[$regionKey])) {
                    $id = $this->reserveId('region', 'region_id');
                    $newRegions[] = [$id, 'Reg_' . $nextRegionUuid++, $regionName, $countryId, $now, $now];
                    $regionMap[$regionKey] = $id;
                }
                $regionId = $regionMap[$regionKey];

                $districtKey = $regionId . '|' . strtoupper($districtName);
                if (!isset($districtMap[$districtKey])) {
                    $id = $this->reserveId('district', 'district_id');
                    $newDistricts[] = [$id, 'Dis_' . $nextDistrictUuid++, $regionId, $districtName, $now, $now];
                    $districtMap[$districtKey] = $id;
                }
                $districtId = $districtMap[$districtKey];

                if ($wardName !== '') {
                    $wardKey = $districtId . '|' . strtoupper($wardName);
                    if (!isset($wardMap[$wardKey])) {
                        $id = $this->reserveId('ward', 'ward_id');
                        $newWards[] = [$id, 'Ward_' . $nextWardUuid++, $wardName, $regionId, $districtId, $now, $now];
                        $wardMap[$wardKey] = $id;
                    }
                }

                if ($streetName !== '') {
                    $streetKey = $districtId . '|' . strtoupper($streetName);
                    if (!isset($streetMap[$streetKey])) {
                        $id = $this->reserveId('street', 'street_id');
                        $newStreets[] = [$id, 'Stre_' . $nextStreetUuid++, $streetName, $regionId, $districtId, $now, $now];
                        $streetMap[$streetKey] = $id;
                    }
                }
            }
            fclose($fh);
        }

        $this->batchInsertChunked('region', ['region_id', 'uuid', 'name', 'country_id', 'created_at', 'updated_at'], $newRegions);
        $this->batchInsertChunked('district', ['district_id', 'uuid', 'region_id', 'district_name', 'created_at', 'updated_at'], $newDistricts);
        $this->batchInsertChunked('ward', ['ward_id', 'uuid', 'ward_name', 'region_id', 'district_id', 'created_at', 'updated_at'], $newWards);
        $this->batchInsertChunked('street', ['street_id', 'uuid', 'street_name', 'region_id', 'district_id', 'created_at', 'updated_at'], $newStreets);

        echo "  > seeded " . count($newRegions) . " regions, " . count($newDistricts) . " districts, "
            . count($newWards) . " wards, " . count($newStreets) . " streets\n";
    }

    public function safeDown()
    {
        echo "  > this seed migration cannot be safely reversed automatically (would risk deleting properties' streets); skipping.\n";
        return true;
    }

    private $reservedIds = [];

    private function reserveId($table, $pk)
    {
        if (!isset($this->reservedIds[$table])) {
            $max = (new \yii\db\Query())->from($table)->max($pk);
            $this->reservedIds[$table] = (int) $max;
        }
        return ++$this->reservedIds[$table];
    }

    private function nextUuidNumber($table, $uuidCol, $prefix)
    {
        $max = (new \yii\db\Query())
            ->from($table)
            ->where(['like', $uuidCol, $prefix . '%', false])
            ->max("CAST(SUBSTRING($uuidCol, " . (strlen($prefix) + 1) . ") AS UNSIGNED)");

        return ((int) $max) + 1;
    }

    private function batchInsertChunked($table, $columns, $rows)
    {
        if (empty($rows)) {
            return;
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            $this->batchInsert($table, $columns, $chunk);
        }
    }
}
