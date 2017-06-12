<?php

namespace PorchaProcessingBundle\Util;

class PlaceHolders
{
    private static $fields = array(
        'form_no' => 'ফরম নং',
        'ref_page_no' => 'রেফারেন্স পেজ নং', //unknown field, not defined yet
        'district' => 'জেলা ',
        'thana' => 'থানা ',
        'upozila' => 'উপজেলা ',
        'pargana' => 'পরগনা ',
        'mouza' => 'মৌজা ',
        'jl_no' => 'জেঃ এলঃ নং ',
        'rs_no' => 'রেঃ সাঃ নং ',
        'khatian_no' => 'খতিয়ান নং ',
        'taugi_no' => 'তৌজি নং',
        'thana_oyar_nong' => 'থানা ওয়ার নং',
        'police_station' => 'পুলিশ ষ্টেসন',
        'sottadhikari_shreni' => 'স্বত্বাধিকারীর শ্রেণী',
        'otro_sotter_biboron_jot' => 'অত্র স্বত্বের বিবরণ > জোত',
        'sotter_staitto' => 'স্বত্বের স্থায়ীত্ব',
        'saback_jl_no' => 'সাবেক জেঃ এলঃ নং ',
        'sa_khatian_no' => 'এস এ খতিয়ান নং',
        'otro_sotter_cromik_nombor' => 'অত্র স্বত্বের ক্রমিক নম্বর',

        //CS
        'ups_dakholkar_sangkhipto' => 'উপরিস্থ স্বত্ত্বের > দখলকার/বিবরণ ও দখলকার সংক্ষিপ্ত',
        'ups_porospor_ongso' => 'উপরিস্থ স্বত্ত্বের > পরস্পর অংশ',
        'khatian_nong_mey_bata' => 'উপরিস্থ স্বত্বের > খতিয়ান নং মায় বাটা',
        'otro_sotter_deyo_khajana' => 'অত্র স্বত্ত্বের দেয় > খাজানা',
        'otro_sotter_deyo_ses' => 'অত্র স্বত্ত্বের দেয় > সেস',
        'otro_sotter_deyo_poth_pritta' => 'অত্র স্বত্ত্বের দেয় > পথ ও পৃত্ত',
        'otro_sotter_deyo_shikkha_ses' => 'অত্র স্বত্ত্বের দেয় > শিক্ষা  সেস',
        'mantobbo_prothom_pata' => 'মন্তব্য (প্রথম পাতা)',
        'dhara_mote' => 'ধারা মতে',
        'kon_son_hoite' => 'কোন সন/তারিখ হইতে বা যে সন/তারিখ হইতে আমলে আসিবে',
        'kon_son_hoite_khajana' => 'ধারা মতে ও কোন সন/তারিখ হইতে > খাজানা',
        'kon_son_hoite_ses' => 'ধারা মতে ও কোন সন/তারিখ হইতে > সেস',
        'khatian_numberer_bata' => 'অত্র স্বত্বের > খতিয়ান নম্বরের বাটা',
        'ots_dokholkar' => 'অত্র স্বত্ত্বের বিবরণ ও দখলকার',
        'ots_dokholkar_2' => 'অত্র স্বত্বের বিবরণ ও দখলকার (দ্বিতীয় কলাম)',
        'ots_dokholkar_ongso' => 'অত্র স্বত্ত্বের বিবরণ ও দখলকার > অংশ',
        'ots_dokholkar_ongso_2' => 'অত্র স্বত্বের বিবরণ ও দখলকার > অংশ (দ্বিতীয় কলাম)',
        'sotter_shreni_obiboron' => 'স্বত্ত্বের শ্রেণী ও বিবরণ',
        'modsto_cirostaie_ki_na' => 'মধ্যস্বত্বাধিকারী হইলে মধ্যস্বত্ব চিরস্থায়ী কিনা',
        'modsto_khajana_joggo_ki_na' => 'মধ্যস্বত্ব বর্তমান থাকিলে খাজানা বৃদ্ধির যোগ্য কি না',
        'kon_shrenir_rayot' => 'রায়ত হইলে কোন শ্রেণীর রায়ত',
        'ots_shreni_niyom_onusongo' => 'অত্র স্বত্ত্বের শ্রেণী এবং বিশেষ নিয়ম ও অনুসঙ্গ',
        'dharamota_note_poriborton' => 'ধারা মতে নোট পরিবর্ত্তন',
        'dharamota_mokaddoma_nong_son' => 'ধারা মতে নোট পরিবর্ত্তন (মায় মোকর্দ্দমা নম্বর এবং সন)>(প্রথম পাতা)',
        'dharamota_mokaddoma_nong_son_2' => 'ধারা মতে নোট পরিবর্ত্তন (মায় মোকর্দ্দমা নম্বর এবং সন)>(দ্বিতীয় পাতা)',
        'dag_nong' => 'দাগ নং',
        'uttor_simaner_dager_dakholkar' => 'উত্তর সীমানার দাগের দখলকার',
        'uttor_simaner_dager_nombor' => 'উত্তর সীমানা > দাগের নম্বর',
        'jomir_rokom' => 'জমির রকম/শ্রেণী',
        'jomir_rokom_krishi' => 'জমির রকম/শ্রেণী > কৃষি',
        'jomir_rokom_okrishi' => 'জমির রকম/শ্রেণী > অকৃষি',
        'mantobbo_ditiyo_pata' => 'মন্তব্য (দ্বিতীয় পাতা)',
        'dager_mot_poriman_akor' => 'দাগের মোট পরিমাণ > একর',
        'dager_mot_poriman_shotangsho' => 'দাগের মোট পরিমাণ > শতক/শতাংশ',
        'dager_modda_otro_khatian_ongso' => 'দাগের মধ্যে অত্র স্বত্ত্বের/খতিয়ানের অংশ',
        'ongsanojae_jomi_poriman_akor' => 'দাগের মধ্যে অত্র স্বত্ত্বের অংশের/অংশানুযায়ী  জমির পরিমাণ > একর',
        'ongsanojae_jomi_poriman_shotok' => 'দাগের মধ্যে অত্র স্বত্ত্বের অংশের/অংশানুযায়ী  জমির পরিমাণ > শতক/শতাংশ',
        'nicosto_sotto_nombor' => 'নীচস্থ স্বত্ব > নম্বর',
        'nicosto_sotto_poricoy_dakol' => 'নীচস্থ স্বত্ব > পরিচয় ও দখল',
        'nicosto_sotto_khajana' => 'নীচস্থ স্বত্ব > খাজানা',
        'nicosto_sotto_mantobbo' => 'নীচস্থ স্বত্ব > মন্তব্য',
        'dakholio_jomir_poriman_akor' => 'নিজ দখলীয় জমির মোট পরিমাণ > একর',
        'dakholio_jomir_poriman_shotok' => 'নিজ দখলীয় জমির মোট পরিমাণ > শতক/শতাংশ',
        'khajana_prapoker_khatian_nong' => 'অধীনস্থ স্বত্ত্বের খাজানা প্রাপকের খতিয়ান নম্বর',
        'ods_bibhino_khatian_nong' => 'অধীনস্থ স্বত্বের  বিভিন্ন খতিয়ানের নম্বর /ফদ্দ বা নীচস্থ স্বত্বের তালিকা',
        'ods_mot_poriman_akor' => 'অধীনস্থ স্বত্ত্বের মোট পরিমাণ > একর',
        'ods_mot_poriman_shotok' => 'অধীনস্থ স্বত্ত্বের মোট পরিমাণ > শতক',
        'sorbo_mot_akor' => 'সর্ব মোট > একর',
        'sorbo_mot_shotok' => 'সর্ব মোট > শতক',

        //RS
        'ejaradarer_num_thikana'=>'মালিক, অকৃষি প্রজা বা ইজারাদারের নাম ও ঠিকানা',
        'ejaradarer_num_thikana_ongso'=>'মালিক, অকৃষি প্রজা বা ইজারাদারের নাম ও ঠিকানা > অংশ',
        'rajoso'=>'রাজস্ব',
        'rajoso_taka'=>'রাজস্ব> টাকা ',
        'rajoso_poysa'=>'রাজস্ব> পয়সা ',
        'rajoso_ja_tarikh_a_adiya_asibe'=>'ধারামতে ধার্য রাজস্ব এবং যে তারিখ হইতে আদায়ে আসিবে',

        'onnanno_mantobbo' =>'দখল বিষয়ক বা অন্যান্য বিশেষ মন্তব্য',
        'ejaradarer_ongsho_mot' => 'মোট অংশ',
        'mot_jomi_akor' => 'মোট জমি একর',
        'mot_jomi_shotangsho' => 'মোট জমি শতাংশ',
    );

    private static $labels = array(

    );

    private static $fieldAttr = array(
        'district' => array('type' => 'text', 'attr_class' => 'form-control', 'read_only' => true, 'mapped' => false),
        'thana' => array('type' => 'text', 'attr_class' => 'form-control', 'read_only' => true, 'mapped' => false),
        'upozila' => array('type' => 'text', 'attr_class' => 'form-control', 'read_only' => true, 'mapped' => false),
        'mouza' => array('type' => 'text', 'attr_class' => 'form-control', 'read_only' => true, 'mapped' => false),
        'jl_no' => array('type' => 'text', 'attr_class' => 'form-control', 'read_only' => true, 'mapped' => false),

        'pargana' => array('type' => 'text', 'attr_class' => 'form-control', 'read_only' => true, 'mapped' => false),
        'rs_no' => array('type' => 'text', 'attr_class' => 'form-control', 'required' => true),
        'taugi_no' => array('type' => 'text', 'attr_class' => 'form-control'),
        'thana_oyar_nong' => array('type' => 'text', 'attr_class' => 'form-control'),
        'police_station' => array('type' => 'text', 'attr_class' => 'form-control'),
        'khatian_no' => array('type' => 'text', 'attr_class' => 'form-control', 'required' => true),
        'otro_sotter_biboron_jot' => array('type' => 'text', 'attr_class' => 'form-control'),
        'sotter_staitto' => array('type' => 'text', 'attr_class' => 'form-control'),
        'sottadhikari_shreni' => array('type' => 'text', 'attr_class' => 'form-control'),
        'saback_jl_no' => array('type' => 'text', 'attr_class' => 'form-control'),
        'mot_jomi_akor' => array('type' => 'text', 'attr_class' => 'form-control'),
        'mot_jomi_shotangsho' => array('type' => 'text', 'attr_class' => 'form-control'),
        'ods_mot_poriman_akor' => array('type' => 'text', 'attr_class' => 'form-control'),
        'ods_mot_poriman_shotok' => array('type' => 'text', 'attr_class' => 'form-control'),
        'sorbo_mot_akor' => array('type' => 'text', 'attr_class' => 'form-control'),
        'sorbo_mot_shotok' => array('type' => 'text', 'attr_class' => 'form-control'),
        'dakholio_jomir_poriman_akor' => array('type' => 'text', 'attr_class' => 'form-control'),
        'dakholio_jomir_poriman_shotok' => array('type' => 'text', 'attr_class' => 'form-control'),
        'ejaradarer_ongsho_mot' => array('type' => 'text', 'attr_class' => 'form-control'),
        'dhara_mote' => array('type' => 'text', 'attr_class' => 'input-xsmall', 'attr_placeholder' => 'ধারা'),
        'ref_page_no' => array('type' => 'text', 'attr_class' => 'input-xsmall', 'attr_placeholder' => 'পেজ নং'),
        'sa_khatian_no' => array('type' => 'text', 'attr_class' => 'input-xsmall', 'attr_placeholder' => 'এস এ খতিয়ান নং'),
//        'dharamota_note_poribort' => array('type' => 'text', 'attr_class' => 'input-xsmall'),
        'form_no' => array('type' => 'text', 'attr_class' => 'form-control input-medium', 'attr_placeholder' => 'ফরম নং '),
        'otro_sotter_cromik_nombor' => array('type' => 'text', 'attr_class' => 'form-control input-medium'),
    );

    public static function  getAll() {
        return array_merge(self::$fields, self::$labels);
    }

    /**
     * @return array
     */
    public static function getFields()
    {
        return self::$fields;
    }

    /**
     * @return array
     */
    public static function getLabels()
    {
        return self::$labels;
    }

    public static function getFieldAttr($field)
    {
        return isset(self::$fieldAttr[$field]) ? self::$fieldAttr[$field] : array(
            'type' => 'textarea',
            'attr_class' => 'form-control entry-area',
            'attr_cols' => '5',
            'attr_rows' => '10',
            'read_only' => false,
            'trim' => false
        );
    }

    public static function  numberConvert($number) {

        $search_array = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        $replace_array = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
        return str_replace($search_array, $replace_array, $number);
    }

    public static function  numberConvertEnglish($number) {

        $search_array = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
        $replace_array = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        return str_replace($search_array, $replace_array, $number);
    }

    public static function getKhatianPageColumnPropertyMapping()
    {
        return array(
            'district' => 'district',
            'upozila' => 'upozila',
            'thana' => 'thana',
            'pargana' => 'pargana',
            'mouza' => 'mouza',
            'jl_no' => 'jl_no',
            'rs_no' => 'rs_no',
            'khatian_no' => 'khatian_no',
            'taugi_no' => 'taugi_no',
            'form_no'                        => 'formNo',
            'thana_oyar_nong'                => 'thanaOyarNong',
            'police_station'                => 'policeStation',
            'ref_page_no'                    => 'refPageNo',
            'sa_khatian_no'                  => 'saKhatianNo',
            'otro_sotter_cromik_nombor'      => 'otroSotterCromikNombor',
            'saback_jl_no'                   => 'sabackJlNo',
            'ejaradarer_num_thikana'         => 'ejaradarerNumThikana',
            'sottadhikari_shreni'            => 'sottadhikariShreni',
            'otro_sotter_biboron_jot'        => 'otroSotterBiboronJot',
            'sotter_staitto'        => 'sotterStaitto',
            'ejaradarer_num_thikana_ongso'   => 'ejaradarerNumThikanaOngsho',
            'rajoso'                         => 'rajoso',
            'rajoso_taka'                    => 'rajosoTaka',
            'rajoso_poysa'                   => 'rajosoPoysa',
            'rajoso_ja_tarikh_a_adiya_asibe' => 'darjoRajosoJaTarikhHoitaAdiyaAsibe',
            'dag_nong'                       => 'dagNong',
            'jomir_rokom'                    => 'jomirRokom',
            'jomir_rokom_krishi'             => 'jomirRokomKrishi',
            'jomir_rokom_okrishi'            => 'jomirRokomOkrishi',
            'dager_mot_poriman_akor'         => 'dagerMotPorimanAkor',
            'dager_mot_poriman_shotangsho'   => 'dagerMotPorimanShotangsho',
            'dager_modda_otro_khatian_ongso' => 'dagerModdaOtroKhatianOngsho',
            'ongsanojae_jomi_poriman_akor'   => 'ongshaOnojayeJomirPorimanAkor',
            'ongsanojae_jomi_poriman_shotok' => 'ongshaOnojayeJomirPorimanShotangsho',
            'nicosto_sotto_nombor'           => 'nicostoSottoNombor',
            'nicosto_sotto_poricoy_dakol'    => 'nicostoSottoPoricoyDakol',
            'nicosto_sotto_khajana'          => 'nicostoSottoKhajana',
            'nicosto_sotto_mantobbo'         => 'nicostoSottoMantobbo',
            'onnanno_mantobbo'               => 'onnannoMantobbo',
            'mot_jomi_akor'                  => 'motJomiAkor',
            'mot_jomi_shotangsho'            => 'motJomiShotangsho',
            'ejaradarer_ongsho_mot'          => 'ejaradarerNumThikanaOngshoMot',
            'khatian_nong_mey_bata'          => 'khatianNongMeyBata',
            'ups_dakholkar_sangkhipto'       => 'uparisthoSotterBiboronDakholkarSangkhipto',
            'ups_porospor_ongso'             => 'uparisthoSotterporosporOngsho',
            'otro_sotter_deyo_khajana'       => 'otroSotterDeyoKhajana',
            'otro_sotter_deyo_ses'           => 'otroSotterDeyoSes',
            'otro_sotter_deyo_poth_pritta'   => 'otroSotterDeyoPothPritta',
            'otro_sotter_deyo_shikkha_ses'   => 'otroSotterDeyoShikkhaSes',
            'mantobbo_prothom_pata'          => 'mantobboProthomPata',
            'dhara_mote'                     => 'dharaMote',
            'kon_son_hoite'                  => 'konSonHoite',
            'kon_son_hoite_khajana'          => 'konSonHoiteKhajana',
            'kon_son_hoite_ses'              => 'konSonHoiteSes',
            'ots_dokholkar'                  => 'otroSotterBiboronODokholkar',
            'ots_dokholkar_ongso'            => 'otroSotterBiboronODokholkarOngsho',
            'ots_dokholkar_2'                => 'otroSotterBiboronODokholkar2',
            'ots_dokholkar_ongso_2'          => 'otroSotterBiboronODokholkarOngsho2',
            'sotter_shreni_obiboron'         => 'sotterShreniOBiboron',
            'modsto_cirostaie_ki_na'         => 'modstoCirostaieKiNa',
            'modsto_khajana_joggo_ki_na'     => 'modstoKhajanaJoggoKiNa',
            'kon_shrenir_rayot'              => 'konShrenirRayot',
            'ots_shreni_niyom_onusongo'      => 'otroSotterShreniNiyomOnusongo',
            'dharamota_mokaddoma_nong_son'   => 'dharamotaNotePoribortonMokaddomaNongAbongSon',
            'dharamota_note_poriborton'      => 'dharamotaNotePoriborton',
            'dharamota_mokaddoma_nong_son_2' => 'dharamotaNotePoribortonMokaddomaNongAbongSonDitiyoPata',
            'mantobbo_ditiyo_pata'           => 'mantobboDitiyoPata',
            'dakholio_jomir_poriman_akor'    => 'nijDakholiyoJomirMotPorimanAkor',
            'dakholio_jomir_poriman_shotok'  => 'nijDakholiyoJomirMotPorimanShotangsho',
            'khajana_prapoker_khatian_nong'  => 'odhinosthoSotterKhajanaPrapokerKhatianNumber',
            'ods_bibhino_khatian_nong'       => 'odhinosthoSotterBibhinnoKhatianerNumber',
            'ods_mot_poriman_akor'           => 'odhinosthoSotterMotPorimanAkor',
            'ods_mot_poriman_shotok'         => 'odhinosthoSotterMotPorimanShotangsho',
            'sorbo_mot_shotok'               => 'sorboMotShotok',
            'sorbo_mot_akor'                 => 'sorboMotAkor',
            'uttor_simaner_dager_dakholkar'  => 'uttorSimanerDagerDakholkar',
            'uttor_simaner_dager_nombor'     => 'uttorSimanerDagerNombor',
            'ref_id'                         => 'refId',
            'khatian_numberer_bata'          => 'khatianNumbererBata',
        );
    }
}