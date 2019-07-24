<?php

namespace app\models\crm;

use Yii;

/**
 * This is the model class for table "movistar.prospecto".
 *
 * @property integer $id
 * @property integer $id_instrumento
 * @property integer $id_data
 * @property integer $barrida
 * @property string $c001
 * @property string $c002
 * @property string $c003
 * @property string $c004
 * @property string $c005
 * @property string $c006
 * @property string $c007
 * @property string $c008
 * @property string $c009
 * @property string $c010
 * @property string $c011
 * @property string $c012
 * @property string $c013
 * @property string $c014
 * @property string $c015
 * @property string $c016
 * @property string $c017
 * @property string $c018
 * @property string $c019
 * @property string $c020
 * @property string $c021
 * @property string $c022
 * @property string $c023
 * @property string $c024
 * @property string $c025
 * @property string $c026
 * @property string $c027
 * @property string $c028
 * @property string $c029
 * @property string $c030
 * @property string $c031
 * @property string $c032
 * @property string $c033
 * @property string $c034
 * @property string $c035
 * @property string $c036
 * @property string $c037
 * @property string $c038
 * @property string $c039
 * @property string $c040
 * @property string $c041
 * @property string $c042
 * @property string $c043
 * @property string $c044
 * @property string $c045
 * @property string $c046
 * @property string $c047
 * @property string $c048
 * @property string $c049
 * @property string $c050
 * @property string $c051
 * @property string $c052
 * @property string $c053
 * @property string $c054
 * @property string $c055
 * @property string $c056
 * @property string $c057
 * @property string $c058
 * @property string $c059
 * @property string $c060
 * @property string $c061
 * @property string $c062
 * @property string $c063
 * @property string $c064
 * @property string $c065
 * @property string $c066
 * @property string $c067
 * @property string $c068
 * @property string $c069
 * @property string $c070
 * @property string $c071
 * @property string $c072
 * @property string $c073
 * @property string $c074
 * @property string $c075
 * @property string $c076
 * @property string $c077
 * @property string $c078
 * @property string $c079
 * @property string $c080
 * @property string $c081
 * @property string $c082
 * @property string $c083
 * @property string $c084
 * @property string $c085
 * @property string $c086
 * @property string $c087
 * @property string $c088
 * @property string $c089
 * @property string $c090
 * @property string $c091
 * @property string $c092
 * @property string $c093
 * @property string $c094
 * @property string $c095
 * @property string $c096
 * @property string $c097
 * @property string $c098
 * @property string $c099
 * @property string $llamar
 * @property string $reg
 * @property string $up
 * @property integer $st
 */
class Prospecto extends \yii\db\ActiveRecord
{
  
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_instrumento', 'id_data'], 'required'],
            [['id_instrumento', 'id_data', 'barrida', 'st'], 'integer'],
            [['llamar', 'reg', 'up'], 'safe'],
            [['c001', 'c002', 'c003', 'c004', 'c005', 'c006', 'c007', 'c008', 'c009', 'c010', 'c011', 'c012', 'c013', 'c014', 'c015', 'c016', 'c017', 'c018', 'c019', 'c020', 'c021', 'c022', 'c023', 'c024', 'c025', 'c026', 'c027', 'c028', 'c029', 'c030', 'c031', 'c032', 'c033', 'c034', 'c035', 'c036', 'c037', 'c038', 'c039', 'c040', 'c041', 'c042', 'c043', 'c044', 'c045', 'c046', 'c047', 'c048', 'c049', 'c050', 'c051', 'c052', 'c053', 'c054', 'c055', 'c056', 'c057', 'c058', 'c059', 'c060', 'c061', 'c062', 'c063', 'c064', 'c065', 'c066', 'c067', 'c068', 'c069', 'c070', 'c071', 'c072', 'c073', 'c074', 'c075', 'c076', 'c077', 'c078', 'c079', 'c080', 'c081', 'c082', 'c083', 'c084', 'c085', 'c086', 'c087', 'c088', 'c089', 'c090', 'c091', 'c092', 'c093', 'c094', 'c095', 'c096', 'c097', 'c098', 'c099'], 'string', 'max' => 2048],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_instrumento' => 'Id Instrumento',
            'id_data' => 'Id Data',
            'barrida' => 'Barrida',
            'c001' => 'C001',
            'c002' => 'C002',
            'c003' => 'C003',
            'c004' => 'C004',
            'c005' => 'C005',
            'c006' => 'C006',
            'c007' => 'C007',
            'c008' => 'C008',
            'c009' => 'C009',
            'c010' => 'C010',
            'c011' => 'C011',
            'c012' => 'C012',
            'c013' => 'C013',
            'c014' => 'C014',
            'c015' => 'C015',
            'c016' => 'C016',
            'c017' => 'C017',
            'c018' => 'C018',
            'c019' => 'C019',
            'c020' => 'C020',
            'c021' => 'C021',
            'c022' => 'C022',
            'c023' => 'C023',
            'c024' => 'C024',
            'c025' => 'C025',
            'c026' => 'C026',
            'c027' => 'C027',
            'c028' => 'C028',
            'c029' => 'C029',
            'c030' => 'C030',
            'c031' => 'C031',
            'c032' => 'C032',
            'c033' => 'C033',
            'c034' => 'C034',
            'c035' => 'C035',
            'c036' => 'C036',
            'c037' => 'C037',
            'c038' => 'C038',
            'c039' => 'C039',
            'c040' => 'C040',
            'c041' => 'C041',
            'c042' => 'C042',
            'c043' => 'C043',
            'c044' => 'C044',
            'c045' => 'C045',
            'c046' => 'C046',
            'c047' => 'C047',
            'c048' => 'C048',
            'c049' => 'C049',
            'c050' => 'C050',
            'c051' => 'C051',
            'c052' => 'C052',
            'c053' => 'C053',
            'c054' => 'C054',
            'c055' => 'C055',
            'c056' => 'C056',
            'c057' => 'C057',
            'c058' => 'C058',
            'c059' => 'C059',
            'c060' => 'C060',
            'c061' => 'C061',
            'c062' => 'C062',
            'c063' => 'C063',
            'c064' => 'C064',
            'c065' => 'C065',
            'c066' => 'C066',
            'c067' => 'C067',
            'c068' => 'C068',
            'c069' => 'C069',
            'c070' => 'C070',
            'c071' => 'C071',
            'c072' => 'C072',
            'c073' => 'C073',
            'c074' => 'C074',
            'c075' => 'C075',
            'c076' => 'C076',
            'c077' => 'C077',
            'c078' => 'C078',
            'c079' => 'C079',
            'c080' => 'C080',
            'c081' => 'C081',
            'c082' => 'C082',
            'c083' => 'C083',
            'c084' => 'C084',
            'c085' => 'C085',
            'c086' => 'C086',
            'c087' => 'C087',
            'c088' => 'C088',
            'c089' => 'C089',
            'c090' => 'C090',
            'c091' => 'C091',
            'c092' => 'C092',
            'c093' => 'C093',
            'c094' => 'C094',
            'c095' => 'C095',
            'c096' => 'C096',
            'c097' => 'C097',
            'c098' => 'C098',
            'c099' => 'C099',
            'llamar' => 'Llamar',
            'reg' => 'Reg',
            'up' => 'Up',
            'st' => 'St',
        ];
    }
}
