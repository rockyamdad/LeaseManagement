<?php

namespace PorchaProcessingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * KhatianPage
 *
 * @ORM\Table(name="khatian_pages", indexes={
 * @ORM\Index(name="page_type_idx", columns={"page_type"}),
 * @ORM\Index(name="khatian_version_idx", columns={"khatian_version"})
 * })
 * @ORM\Entity(repositoryClass="PorchaProcessingBundle\Repository\KhatianPageRepository")
 */
class KhatianPage
{
    public $prependNewLine = false;
    /**
     * @ORM\OneToOne(targetEntity="PorchaProcessingBundle\Entity\KhatianCorrectionLog", mappedBy="khatianPage")
     */
    protected $correctionLog;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\KhatianVersion")
     * @ORM\JoinColumn(name="khatian_version", referencedColumnName="id")
     */
    private $khatianVersion;
    /**
     * @ORM\ManyToOne(targetEntity="PorchaProcessingBundle\Entity\OfficeTemplate")
     * @ORM\JoinColumn(name="office_template_id", referencedColumnName="id", nullable=true)
     */
    private $officeTemplate;
    /**
     * @var array $type
     * Values (
     'PAGE1',
     'PAGE1_ADDITIONAL',
     'PAGE2',
     'PAGE2_ADDITIONAL'
     * )
     * @ORM\Column(name="page_type", type="string", length=255)
     */
    private $type;
    /**
     * @var integer
     *
     * @ORM\Column(name="page_order", type="smallint", nullable=true)
     */
    private $pageOrder;
    /**
     * @var string
     *
     * @ORM\Column(name="form_no", type="string", nullable=true)
     */
    private $formNo;
    /**
     * @var string
     *
     * @ORM\Column(name="thana_oyar_nong", type="string", nullable=true)
     */
    private $thanaOyarNong;
    /**
     * @var string
     *
     * @ORM\Column(name="police_station", type="string", nullable=true)
     */
    private $policeStation;
    /**
     * @var string
     *
     * @ORM\Column(name="ref_page_no", type="string", nullable=true)
     */
    private $refPageNo;

    /**
     * @var string
     *
     * @ORM\Column(name="otro_sotter_cromik_nombor", type="string", nullable=true)
     */
    private $otroSotterCromikNombor;


    /*==========RS KHATIAN FIELDS START================*/
    /**
     * @var string
     *
     * @ORM\Column(name="sa_khatian_no", type="string", nullable=true)
     */
    private $saKhatianNo;
    /**
     * @var string
     *
     * @ORM\Column(name="saback_jl_no", type="text", nullable=true)
     */
    private $sabackJlNo;
    /**
     * @var string
     *
     * @ORM\Column(name="ejaradarer_num_thikana", type="text", nullable=true)
     */
    private $ejaradarerNumThikana;
    /**
     * @var string
     *
     * @ORM\Column(name="sottadhikari_shreni", type="text", nullable=true)
     */
    private $sottadhikariShreni;
    /**
     * @var string
     *
     * @ORM\Column(name="otro_sotter_biboron_jot", type="text", nullable=true)
     */
    private $otroSotterBiboronJot;
    /**
     * @var string
     *
     * @ORM\Column(name="sotter_staitto", type="text", nullable=true)
     */
    private $sotterStaitto;
    /**
     * @var string
     *
     * @ORM\Column(name="ejaradarer_num_thikana_ongso", type="text", nullable=true)
     */
    private $ejaradarerNumThikanaOngsho;
    /**
     * @var string
     *
     * @ORM\Column(name="rajoso", type="text", nullable=true)
     */
    private $rajoso;
    /**
     * @var string
     *
     * @ORM\Column(name="rajoso_taka", type="text", nullable=true)
     */
    private $rajosoTaka;
    /**
    /**
     * @var string
     *
     * @ORM\Column(name="rajoso_poysa", type="text", nullable=true)
     */
    private $rajosoPoysa;
    /**
    /**
     * @var string
     *
     * @ORM\Column(name="rajoso_ja_tarikh_a_adiya_asibe", type="text", nullable=true)
     */
    private $darjoRajosoJaTarikhHoitaAdiyaAsibe;
    /**
     * @var string
     *
     * @ORM\Column(name="dag_nong", type="text", nullable=true)
     */
    private $dagNong;
    /**
     * @var string
     *
     * @ORM\Column(name="jomir_rokom", type="text", nullable=true)
     */
    private $jomirRokom;
    /**
     * @var string
     *
     * @ORM\Column(name="jomir_rokom_krishi", type="text", nullable=true)
     */
    private $jomirRokomKrishi;
    /**
     * @var string
     *
     * @ORM\Column(name="jomir_rokom_okrishi", type="text", nullable=true)
     */
    private $jomirRokomOkrishi;
    /**
     * @var string
     *
     * @ORM\Column(name="dager_mot_poriman_akor", type="text", nullable=true)
     */
    private $dagerMotPorimanAkor;
    /**
     * @var string
     *
     * @ORM\Column(name="dager_mot_poriman_shotangsho", type="text", nullable=true)
     */
    private $dagerMotPorimanShotangsho;
    /**
     * @var string
     *
     * @ORM\Column(name="dager_modda_otro_khatian_ongso", type="text", nullable=true)
     */
    private $dagerModdaOtroKhatianOngsho;
    /**
     * @var string
     *
     * @ORM\Column(name="ongsanojae_jomi_poriman_akor", type="text", nullable=true)
     */
    private $ongshaOnojayeJomirPorimanAkor;
    /**
     * @var string
     *
     * @ORM\Column(name="ongsanojae_jomi_poriman_shotok", type="text", nullable=true)
     */
    private $ongshaOnojayeJomirPorimanShotangsho;
    /**
     * @var string
     *
     * @ORM\Column(name="nicosto_sotto_nombor", type="text", nullable=true)
     */
    private $nicostoSottoNombor;
    /**
     * @var string
     *
     * @ORM\Column(name="nicosto_sotto_poricoy_dakol", type="text", nullable=true)
     */
    private $nicostoSottoPoricoyDakol;
    /**
     * @var string
     *
     * @ORM\Column(name="nicosto_sotto_khajana", type="text", nullable=true)
     */
    private $nicostoSottoKhajana;
    /**
     * @var string
     *
     * @ORM\Column(name="nicosto_sotto_mantobbo", type="text", nullable=true)
     */
    private $nicostoSottoMantobbo;
    /**
     * @var string
     *
     * @ORM\Column(name="onnanno_mantobbo", type="text", nullable=true)
     */
    private $onnannoMantobbo;
    /**
     * @var string
     *
     * @ORM\Column(name="mot_jomi_akor", type="text", nullable=true)
     */
    private $motJomiAkor;
    /**
     * @var string
     *
     * @ORM\Column(name="mot_jomi_shotangsho", type="text", nullable=true)
     */
    private $motJomiShotangsho;
    /**
     * @var string
     *
     * @ORM\Column(name="ejaradarer_ongsho_mot", type="text", nullable=true)
     */
    private $ejaradarerNumThikanaOngshoMot;
    /**
     * @var string
     *
     * @ORM\Column(name="khatian_nong_mey_bata", type="text", nullable=true)
     */
    private $khatianNongMeyBata;
    /**
     * @var string
     *
     * @ORM\Column(name="ups_dakholkar_sangkhipto", type="text", nullable=true)
     */
    private $uparisthoSotterBiboronDakholkarSangkhipto;
    /**
     * @var string
     *
     * @ORM\Column(name="ups_porospor_ongso", type="text", nullable=true)
     */
    private $uparisthoSotterporosporOngsho;
    /**
     * @var string
     *
     * @ORM\Column(name="otro_sotter_deyo_khajana", type="text", nullable=true)
     */
    private $otroSotterDeyoKhajana;

    /*==========RS KHATIAN FIELDS END================*/

    /*==========SA KHATIAN FIELDS START================*/
    /**
     * @var string
     *
     * @ORM\Column(name="otro_sotter_deyo_ses", type="text", nullable=true)
     */
    private $otroSotterDeyoSes;
    /**
     * @var string
     *
     * @ORM\Column(name="otro_sotter_deyo_poth_pritta", type="text", nullable=true)
     */
    private $otroSotterDeyoPothPritta;
    /**
     * @var string
     *
     * @ORM\Column(name="otro_sotter_deyo_shikkha_ses", type="text", nullable=true)
     */
    private $otroSotterDeyoShikkhaSes;
    /**
     * @var string
     *
     * @ORM\Column(name="mantobbo_prothom_pata", type="text", nullable=true)
     */
    private $mantobboProthomPata;
    /**
     * @var string
     *
     * @ORM\Column(name="dhara_mote", type="text", nullable=true)
     */
    private $dharaMote;
    /**
     * @var string
     *
     * @ORM\Column(name="kon_son_hoite", type="text", nullable=true)
     */
    private $konSonHoite;
    /**
     * @var string
     *
     * @ORM\Column(name="kon_son_hoite_khajana", type="text", nullable=true)
     */
    private $konSonHoiteKhajana;
    /**
     * @var string
     *
     * @ORM\Column(name="kon_son_hoite_ses", type="text", nullable=true)
     */
    private $konSonHoiteSes;

    /**
     * @var string
     *
     * @ORM\Column(name="khatian_numberer_bata", type="text", nullable=true)
     */
    private $khatianNumbererBata;
    /**
     * @var string
     *
     * @ORM\Column(name="ots_dokholkar", type="text", nullable=true)
     */
    private $otroSotterBiboronODokholkar;
    /**
     * @var string
     *
     * @ORM\Column(name="ots_dokholkar_ongso", type="text", nullable=true)
     */
    private $otroSotterBiboronODokholkarOngsho;
    /**
     * @var string
     *
     * @ORM\Column(name="ots_dokholkar_2", type="text", nullable=true)
     */
    private $otroSotterBiboronODokholkar2;
    /**
     * @var string
     *
     * @ORM\Column(name="ots_dokholkar_ongso_2", type="text", nullable=true)
     */
    private $otroSotterBiboronODokholkarOngsho2;
    /**
     * @var string
     *
     * @ORM\Column(name="sotter_shreni_obiboron", type="text", nullable=true)
     */
    private $sotterShreniOBiboron;
    /**
     * @var string
     *
     * @ORM\Column(name="modsto_cirostaie_ki_na", type="text", nullable=true)
     */
    private $modstoCirostaieKiNa;
    /**
     * @var string
     *
     * @ORM\Column(name="modsto_khajana_joggo_ki_na", type="text", nullable=true)
     */
    private $modstoKhajanaJoggoKiNa;
    /**
     * @var string
     *
     * @ORM\Column(name="kon_shrenir_rayot", type="text", nullable=true)
     */
    private $konShrenirRayot;
    /**
     * @var string
     *
     * @ORM\Column(name="ots_shreni_niyom_onusongo", type="text", nullable=true)
     */
    private $otroSotterShreniNiyomOnusongo;
    /**
     * @var string
     *
     * @ORM\Column(name="dharamota_mokaddoma_nong_son", type="text", nullable=true)
     */
    private $dharamotaNotePoribortonMokaddomaNongAbongSon;
    /**
     * @var string
     *
     * @ORM\Column(name="dharamota_note_poriborton", type="text", nullable=true)
     */
    private $dharamotaNotePoriborton;
    /**
     * @var string
     *
     * @ORM\Column(name="dharamota_mokaddoma_nong_son_2", type="text", nullable=true)
     */
    private $dharamotaNotePoribortonMokaddomaNongAbongSonDitiyoPata;
    /**
     * @var string
     *
     * @ORM\Column(name="mantobbo_ditiyo_pata", type="text", nullable=true)
     */
    private $mantobboDitiyoPata;
    /**
     * @var string
     *
     * @ORM\Column(name="dakholio_jomir_poriman_akor", type="text", nullable=true)
     */
    private $nijDakholiyoJomirMotPorimanAkor;
    /**
     * @var string
     *
     * @ORM\Column(name="dakholio_jomir_poriman_shotok", type="text", nullable=true)
     */
    private $nijDakholiyoJomirMotPorimanShotangsho;
    /**
     * @var string
     *
     * @ORM\Column(name="khajana_prapoker_khatian_nong", type="text", nullable=true)
     */
    private $odhinosthoSotterKhajanaPrapokerKhatianNumber;
    /**
     * @var string
     *
     * @ORM\Column(name="ods_bibhino_khatian_nong", type="text", nullable=true)
     */
    private $odhinosthoSotterBibhinnoKhatianerNumber;
    /**
     * @var string
     *
     * @ORM\Column(name="ods_mot_poriman_akor", type="text", nullable=true)
     */
    private $odhinosthoSotterMotPorimanAkor;
    /**
     * @var string
     *
     * @ORM\Column(name="ods_mot_poriman_shotok", type="text", nullable=true)
     */
    private $odhinosthoSotterMotPorimanShotangsho;
    /**
     * @var string
     *
     * @ORM\Column(name="sorbo_mot_shotok", type="text", nullable=true)
     */
    private $sorboMotShotok;
    /**
     * @var string
     *
     * @ORM\Column(name="sorbo_mot_akor", type="text", nullable=true)
     */
    private $sorboMotAkor;
    /**
     * @var string
     *
     * @ORM\Column(name="uttor_simaner_dager_dakholkar", type="text", nullable=true)
     */
    private $uttorSimanerDagerDakholkar;
    /**
     * @var string
     *
     * @ORM\Column(name="uttor_simaner_dager_nombor", type="text", nullable=true)
     */
    private $uttorSimanerDagerNombor;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     *
     * @ORM\Column(name="entry_complete", type="boolean")
     */
    private $entryComplete = false;
    /**
     * @var string
     *
     * @ORM\Column(name="ref_id", type="integer", nullable=true)
     */
    private $refId;
    /*==========SA KHATIAN FIELDS END================*/

    /*==========CS KHATIAN FIELDS START================*/

    /**
     * @return mixed
     */
    public function getOfficeTemplate()
    {
        return $this->officeTemplate;
    }
    /*==========CS KHATIAN FIELDS END================*/

    /**
     * @param mixed $officeTemplate
     */
    public function setOfficeTemplate($officeTemplate)
    {
        $this->officeTemplate = $officeTemplate;
    }

    /**
     * @return string
     */
    public function getSaKhatianNo()
    {
        return $this->saKhatianNo;
    }

    /**
     * @param string $saKhatianNo
     */
    public function setSaKhatianNo($saKhatianNo)
    {
        $this->saKhatianNo = $saKhatianNo;
    }

    /**
     * @return int
     */
    public function getPageOrder()
    {
        return $this->pageOrder;
    }

    /**
     * @param int $pageOrder
     */
    public function setPageOrder($pageOrder)
    {
        $this->pageOrder = $pageOrder;
    }

    /**
     * @return KhatianVersion
     */
    public function getKhatianVersion()
    {
        return $this->khatianVersion;
    }

    /**
     * @param mixed $khatianVersion
     */
    public function setKhatianVersion($khatianVersion)
    {
        $this->khatianVersion = $khatianVersion;
    }

    /**
     * @return string
     */
    public function getFormNo()
    {
        return $this->formNo;
    }

    /**
     * @param string $formNo
     */
    public function setFormNo($formNo)
    {
        $this->formNo = $formNo;
    }

    /**
     * @return string
     */
    public function getRefPageNo()
    {
        return $this->refPageNo;
    }

    /**
     * @param string $refPageNo
     */
    public function setRefPageNo($refPageNo)
    {
        $this->refPageNo = $refPageNo;
    }

    /**
     * @return string
     */
    public function getSabackJlNo()
    {
        return $this->sabackJlNo;
    }

    /**
     * @param string $sabackJlNo
     */
    public function setSabackJlNo($sabackJlNo)
    {
        $this->sabackJlNo = $sabackJlNo;
    }

    /**
     * @return string
     */
    public function getEjaradarerNumThikana()
    {
        return $this->sanitize($this->ejaradarerNumThikana);
    }

    /**
     * @param string $ejaradarerNumThikana
     */
    public function setEjaradarerNumThikana($ejaradarerNumThikana)
    {
        $this->ejaradarerNumThikana = $ejaradarerNumThikana;
    }

    private function sanitize($value)
    {
        return $this->prependNewLine && strpos($value, PHP_EOL) === 1 ? PHP_EOL.$value : $value;
    }

    /**
     * @return string
     */
    public function getSottadhikariShreni()
    {
        return $this->sottadhikariShreni;
    }

    /**
     * @param string $sottadhikariShreni
     */
    public function setSottadhikariShreni($sottadhikariShreni)
    {
        $this->sottadhikariShreni = $sottadhikariShreni;
    }

    /**
     * @return string
     */
    public function getOtroSotterBiboronJot()
    {
        return $this->otroSotterBiboronJot;
    }

    /**
     * @param string $otroSotterBiboronJot
     */
    public function setOtroSotterBiboronJot($otroSotterBiboronJot)
    {
        $this->otroSotterBiboronJot = $otroSotterBiboronJot;
    }

    /**
     * @return string
     */
    public function getSotterStaitto()
    {
        return $this->sotterStaitto;
    }

    /**
     * @param string $sotterStaitto
     */
    public function setSotterStaitto($sotterStaitto)
    {
        $this->sotterStaitto = $sotterStaitto;
    }

    /**
     * @return string
     */
    public function getEjaradarerNumThikanaOngsho()
    {
        return $this->sanitize($this->ejaradarerNumThikanaOngsho);
    }

    /**
     * @param string $ejaradarerNumThikanaOngsho
     */
    public function setEjaradarerNumThikanaOngsho($ejaradarerNumThikanaOngsho)
    {
        $this->ejaradarerNumThikanaOngsho = $ejaradarerNumThikanaOngsho;
    }

    /**
     * @return string
     */
    public function getRajoso()
    {
        return $this->sanitize($this->rajoso);
    }

    /**
     * @param string $rajoso
     */
    public function setRajoso($rajoso)
    {
        $this->rajoso = $rajoso;
    }

    /**
     * @return string
     */
    public function getRajosoTaka()
    {
        return $this->sanitize($this->rajosoTaka);
    }

    /**
     * @param string $rajosoTaka
     */
    public function setRajosoTaka($rajosoTaka)
    {
        $this->rajosoTaka = $rajosoTaka;
    }

    /**
     * @return string
     */
    public function getRajosoPoysa()
    {
        return $this->sanitize($this->rajosoPoysa);
    }

    /**
     * @param string $rajosoPoysa
     */
    public function setRajosoPoisa($rajosoPoysa)
    {
        $this->rajosoPoisa = $rajosoPoysa;
    }

    /**
     * @return string
     */
    public function getDarjoRajosoJaTarikhHoitaAdiyaAsibe()
    {
        return $this->sanitize($this->darjoRajosoJaTarikhHoitaAdiyaAsibe);
    }

    /**
     * @param string $darjoRajosoJaTarikhHoitaAdiyaAsibe
     */
    public function setDarjoRajosoJaTarikhHoitaAdiyaAsibe($darjoRajosoJaTarikhHoitaAdiyaAsibe)
    {
        $this->darjoRajosoJaTarikhHoitaAdiyaAsibe = $darjoRajosoJaTarikhHoitaAdiyaAsibe;
    }

    /**
     * @return string
     */
    public function getDagNong()
    {
        return $this->sanitize($this->dagNong);
    }

    /**
     * @param string $dagNong
     */
    public function setDagNong($dagNong)
    {
        $this->dagNong = $dagNong;
    }

    /**
     * @return string
     */
    public function getJomirRokom()
    {
        return $this->sanitize($this->jomirRokom);
    }

    /**
     * @param string $jomirRokom
     */
    public function setJomirRokom($jomirRokom)
    {
        $this->jomirRokom = $jomirRokom;
    }

    /**
     * @return string
     */
    public function getJomirRokomKrishi()
    {
        return $this->sanitize($this->jomirRokomKrishi);
    }

    /**
     * @param string $jomirRokomKrishi
     */
    public function setJomirRokomKrishi($jomirRokomKrishi)
    {
        $this->jomirRokomKrishi = $jomirRokomKrishi;
    }

    /**
     * @return string
     */
    public function getJomirRokomOkrishi()
    {
        return $this->sanitize($this->jomirRokomOkrishi);
    }

    /**
     * @param string $jomirRokomOkrishi
     */
    public function setJomirRokomOkrishi($jomirRokomOkrishi)
    {
        $this->jomirRokomOkrishi = $jomirRokomOkrishi;
    }

    /**
     * @return string
     */
    public function getDagerMotPorimanAkor()
    {
        return $this->sanitize($this->dagerMotPorimanAkor);
    }

    /**
     * @param string $dagerMotPorimanAkor
     */
    public function setDagerMotPorimanAkor($dagerMotPorimanAkor)
    {
        $this->dagerMotPorimanAkor = $dagerMotPorimanAkor;
    }

    /**
     * @return string
     */
    public function getDagerMotPorimanShotangsho()
    {
        return $this->sanitize($this->dagerMotPorimanShotangsho);
    }

    /**
     * @param string $dagerMotPorimanShotangsho
     */
    public function setDagerMotPorimanShotangsho($dagerMotPorimanShotangsho)
    {
        $this->dagerMotPorimanShotangsho = $dagerMotPorimanShotangsho;
    }

    /**
     * @return string
     */
    public function getDagerModdaOtroKhatianOngsho()
    {
        return $this->sanitize($this->dagerModdaOtroKhatianOngsho);
    }

    /**
     * @param string $dagerModdaOtroKhatianOngsho
     */
    public function setDagerModdaOtroKhatianOngsho($dagerModdaOtroKhatianOngsho)
    {
        $this->dagerModdaOtroKhatianOngsho = $dagerModdaOtroKhatianOngsho;
    }

    /**
     * @return string
     */
    public function getOngshaOnojayeJomirPorimanAkor()
    {
        return $this->sanitize($this->ongshaOnojayeJomirPorimanAkor);
    }

    /**
     * @param string $ongshaOnojayeJomirPorimanAkor
     */
    public function setOngshaOnojayeJomirPorimanAkor($ongshaOnojayeJomirPorimanAkor)
    {
        $this->ongshaOnojayeJomirPorimanAkor = $ongshaOnojayeJomirPorimanAkor;
    }

    /**
     * @return string
     */
    public function getOngshaOnojayeJomirPorimanShotangsho()
    {
        return $this->sanitize($this->ongshaOnojayeJomirPorimanShotangsho);
    }

    /**
     * @param string $ongshaOnojayeJomirPorimanShotangsho
     */
    public function setOngshaOnojayeJomirPorimanShotangsho($ongshaOnojayeJomirPorimanShotangsho)
    {
        $this->ongshaOnojayeJomirPorimanShotangsho = $ongshaOnojayeJomirPorimanShotangsho;
    }

    /**
     * @return string
     */
    public function getOnnannoMantobbo()
    {
        return $this->sanitize($this->onnannoMantobbo);
    }

    /**
     * @param string $onnannoMantobbo
     */
    public function setOnnannoMantobbo($onnannoMantobbo)
    {
        $this->onnannoMantobbo = $onnannoMantobbo;
    }

    /**
     * @return string
     */
    public function getMotJomiAkor()
    {
        return $this->sanitize($this->motJomiAkor);
    }

    /**
     * @param string $motJomiAkor
     */
    public function setMotJomiAkor($motJomiAkor)
    {
        $this->motJomiAkor = $motJomiAkor;
    }

    /**
     * @return string
     */
    public function getMotJomiShotangsho()
    {
        return $this->sanitize($this->motJomiShotangsho);
    }

    /**
     * @param string $motJomiShotangsho
     */
    public function setMotJomiShotangsho($motJomiShotangsho)
    {
        $this->motJomiShotangsho = $motJomiShotangsho;
    }

    /**
     * @return string
     */
    public function getEjaradarerNumThikanaOngshoMot()
    {
        return $this->sanitize($this->ejaradarerNumThikanaOngshoMot);
    }

    /**
     * @param string $ejaradarerNumThikanaOngshoMot
     */
    public function setEjaradarerNumThikanaOngshoMot($ejaradarerNumThikanaOngshoMot)
    {
        $this->ejaradarerNumThikanaOngshoMot = $ejaradarerNumThikanaOngshoMot;
    }

    /**
     * @return string
     */
    public function getKhatianNongMeyBata()
    {
        return $this->sanitize($this->khatianNongMeyBata);
    }

    /**
     * @param string $khatianNongMeyBata
     */
    public function setKhatianNongMeyBata($khatianNongMeyBata)
    {
        $this->khatianNongMeyBata = $khatianNongMeyBata;
    }

    /**
     * @return string
     */
    public function getUparisthoSotterBiboronDakholkarSangkhipto()
    {
        return $this->sanitize($this->uparisthoSotterBiboronDakholkarSangkhipto);
    }

    /**
     * @param string $uparisthoSotterBiboronDakholkarSangkhipto
     */
    public function setUparisthoSotterBiboronDakholkarSangkhipto($uparisthoSotterBiboronDakholkarSangkhipto)
    {
        $this->uparisthoSotterBiboronDakholkarSangkhipto = $uparisthoSotterBiboronDakholkarSangkhipto;
    }

    /**
     * @return string
     */
    public function getUparisthoSotterporosporOngsho()
    {
        return $this->sanitize($this->uparisthoSotterporosporOngsho);
    }

    /**
     * @param string $uparisthoSotterporosporOngsho
     */
    public function setUparisthoSotterporosporOngsho($uparisthoSotterporosporOngsho)
    {
        $this->uparisthoSotterporosporOngsho = $uparisthoSotterporosporOngsho;
    }

    /**
     * @return string
     */
    public function getOtroSotterDeyoKhajana()
    {
        return $this->sanitize($this->otroSotterDeyoKhajana);
    }

    /**
     * @param string $otroSotterDeyoKhajana
     */
    public function setOtroSotterDeyoKhajana($otroSotterDeyoKhajana)
    {
        $this->otroSotterDeyoKhajana = $otroSotterDeyoKhajana;
    }

    /**
     * @return string
     */
    public function getOtroSotterDeyoSes()
    {
        return $this->sanitize($this->otroSotterDeyoSes);
    }

    /**
     * @param string $otroSotterDeyoSes
     */
    public function setOtroSotterDeyoSes($otroSotterDeyoSes)
    {
        $this->otroSotterDeyoSes = $otroSotterDeyoSes;
    }

    /**
     * @return string
     */
    public function getOtroSotterDeyoPothPritta()
    {
        return $this->otroSotterDeyoPothPritta;
    }

    /**
     * @param string $otroSotterDeyoPothPritta
     */
    public function setOtroSotterDeyoPothPritta($otroSotterDeyoPothPritta)
    {
        $this->otroSotterDeyoPothPritta = $otroSotterDeyoPothPritta;
    }

    /**
     * @return string
     */
    public function getOtroSotterDeyoShikkhaSes()
    {
        return $this->sanitize($this->otroSotterDeyoShikkhaSes);
    }

    /**
     * @param string $otroSotterDeyoShikkhaSes
     */
    public function setOtroSotterDeyoShikkhaSes($otroSotterDeyoShikkhaSes)
    {
        $this->otroSotterDeyoShikkhaSes = $otroSotterDeyoShikkhaSes;
    }

    /**
     * @return string
     */
    public function getMantobboProthomPata()
    {
        return $this->sanitize($this->mantobboProthomPata);
    }

    /**
     * @param string $mantobboProthomPata
     */
    public function setMantobboProthomPata($mantobboProthomPata)
    {
        $this->mantobboProthomPata = $mantobboProthomPata;
    }

    /**
     * @return string
     */
    public function getDharaMote()
    {
        return $this->dharaMote;
    }

    /**
     * @param string $dharaMote
     */
    public function setDharaMote($dharaMote)
    {
        $this->dharaMote = $dharaMote;
    }

    /**
     * @return string
     */
    public function getKonSonHoiteKhajana()
    {
        return $this->sanitize($this->konSonHoiteKhajana);
    }

    /**
     * @param string $konSonHoiteKhajana
     */
    public function setKonSonHoiteKhajana($konSonHoiteKhajana)
    {
        $this->konSonHoiteKhajana = $konSonHoiteKhajana;
    }

    /**
     * @return string
     */
    public function getKonSonHoiteSes()
    {
        return $this->sanitize($this->konSonHoiteSes);
    }

    /**
     * @param string $konSonHoiteSes
     */
    public function setKonSonHoiteSes($konSonHoiteSes)
    {
        $this->konSonHoiteSes = $konSonHoiteSes;
    }

    /**
     * @return string
     */
    public function getKhatianNumbererBata()
    {
        return $this->khatianNumbererBata;
    }

    /**
     * @param string $khatianNumbererBata
     */
    public function setKhatianNumbererBata($khatianNumbererBata)
    {
        $this->khatianNumbererBata = $khatianNumbererBata;
    }

    /**
     * @return string
     */
    public function getKonSonHoite()
    {
        return $this->konSonHoite;
    }

    /**
     * @param string $konSonHoite
     */
    public function setKonSonHoite($konSonHoite)
    {
        $this->konSonHoite = $konSonHoite;
    }

    /**
     * @return string
     */
    public function getSotterShreniOBiboron()
    {
        return $this->sotterShreniOBiboron;
    }

    /**
     * @param string $sotterShreniOBiboron
     */
    public function setSotterShreniOBiboron($sotterShreniOBiboron)
    {
        $this->sotterShreniOBiboron = $sotterShreniOBiboron;
    }

    /**
     * @return string
     */
    public function getOtroSotterBiboronODokholkar()
    {
        return $this->sanitize($this->otroSotterBiboronODokholkar);
    }

    /**
     * @param string $otroSotterBiboronODokholkar
     */
    public function setOtroSotterBiboronODokholkar($otroSotterBiboronODokholkar)
    {
        $this->otroSotterBiboronODokholkar = $otroSotterBiboronODokholkar;
    }

    /**
     * @return string
     */
    public function getThanaOyarNong()
    {
        return $this->thanaOyarNong;
    }

    /**
     * @param string $thanaOyarNong
     */
    public function setThanaOyarNong($thanaOyarNong)
    {
        $this->thanaOyarNong = $thanaOyarNong;
    }

    /**
     * @return string
     */
    public function getPoliceStation()
    {
        return $this->policeStation;
    }

    /**
     * @param string $policeStation
     */
    public function setPoliceStation($policeStation)
    {
        $this->policeStation = $policeStation;
    }

    /**
     * @return string
     */
    public function getOtroSotterCromikNombor()
    {
        return $this->otroSotterCromikNombor;
    }

    /**
     * @param string $otroSotterCromikNombor
     */
    public function setOtroSotterCromikNombor($otroSotterCromikNombor)
    {
        $this->otroSotterCromikNombor = $otroSotterCromikNombor;
    }

    /**
     * @return string
     */
    public function getNicostoSottoNombor()
    {
        return $this->nicostoSottoNombor;
    }

    /**
     * @param string $nicostoSottoNombor
     */
    public function setNicostoSottoNombor($nicostoSottoNombor)
    {
        $this->nicostoSottoNombor = $nicostoSottoNombor;
    }

    /**
     * @return string
     */
    public function getNicostoSottoPoricoyDakol()
    {
        return $this->nicostoSottoPoricoyDakol;
    }

    /**
     * @param string $nicostoSottoPoricoyDakol
     */
    public function setNicostoSottoPoricoyDakol($nicostoSottoPoricoyDakol)
    {
        $this->nicostoSottoPoricoyDakol = $nicostoSottoPoricoyDakol;
    }

    /**
     * @return string
     */
    public function getNicostoSottoKhajana()
    {
        return $this->nicostoSottoKhajana;
    }

    /**
     * @param string $nicostoSottoKhajana
     */
    public function setNicostoSottoKhajana($nicostoSottoKhajana)
    {
        $this->nicostoSottoKhajana = $nicostoSottoKhajana;
    }

    /**
     * @return string
     */
    public function getNicostoSottoMantobbo()
    {
        return $this->nicostoSottoMantobbo;
    }

    /**
     * @param string $nicostoSottoMantobbo
     */
    public function setNicostoSottoMantobbo($nicostoSottoMantobbo)
    {
        $this->nicostoSottoMantobbo = $nicostoSottoMantobbo;
    }

    /**
     * @return string
     */
    public function getModstoCirostaieKiNa()
    {
        return $this->modstoCirostaieKiNa;
    }

    /**
     * @param string $modstoCirostaieKiNa
     */
    public function setModstoCirostaieKiNa($modstoCirostaieKiNa)
    {
        $this->modstoCirostaieKiNa = $modstoCirostaieKiNa;
    }

    /**
     * @return string
     */
    public function getModstoKhajanaJoggoKiNa()
    {
        return $this->modstoKhajanaJoggoKiNa;
    }

    /**
     * @param string $modstoKhajanaJoggoKiNa
     */
    public function setModstoKhajanaJoggoKiNa($modstoKhajanaJoggoKiNa)
    {
        $this->modstoKhajanaJoggoKiNa = $modstoKhajanaJoggoKiNa;
    }

    /**
     * @return string
     */
    public function getKonShrenirRayot()
    {
        return $this->konShrenirRayot;
    }

    /**
     * @param string $konShrenirRayot
     */
    public function setKonShrenirRayot($konShrenirRayot)
    {
        $this->konShrenirRayot = $konShrenirRayot;
    }

    /**
     * @return string
     */
    public function getUttorSimanerDagerNombor()
    {
        return $this->uttorSimanerDagerNombor;
    }

    /**
     * @param string $uttorSimanerDagerNombor
     */
    public function setUttorSimanerDagerNombor($uttorSimanerDagerNombor)
    {
        $this->uttorSimanerDagerNombor = $uttorSimanerDagerNombor;
    }

    /**
     * @return string
     */
    public function getOtroSotterBiboronODokholkarOngsho()
    {
        return $this->sanitize($this->otroSotterBiboronODokholkarOngsho);
    }

    /**
     * @param string $otroSotterBiboronODokholkarOngsho
     */
    public function setOtroSotterBiboronODokholkarOngsho($otroSotterBiboronODokholkarOngsho)
    {
        $this->otroSotterBiboronODokholkarOngsho = $otroSotterBiboronODokholkarOngsho;
    }

    /**
     * @return string
     */
    public function getOtroSotterBiboronODokholkar2()
    {
        return $this->sanitize($this->otroSotterBiboronODokholkar2);
    }

    /**
     * @param string $otroSotterBiboronODokholkar2
     */
    public function setOtroSotterBiboronODokholkar2($otroSotterBiboronODokholkar2)
    {
        $this->otroSotterBiboronODokholkar2 = $otroSotterBiboronODokholkar2;
    }

    /**
     * @return string
     */
    public function getOtroSotterBiboronODokholkarOngsho2()
    {
        return $this->sanitize($this->otroSotterBiboronODokholkarOngsho2);
    }

    /**
     * @param string $otroSotterBiboronODokholkarOngsho2
     */
    public function setOtroSotterBiboronODokholkarOngsho2($otroSotterBiboronODokholkarOngsho2)
    {
        $this->otroSotterBiboronODokholkarOngsho2 = $otroSotterBiboronODokholkarOngsho2;
    }

    /**
     * @return string
     */
    public function getOtroSotterShreniNiyomOnusongo()
    {
        return $this->sanitize($this->otroSotterShreniNiyomOnusongo);
    }

    /**
     * @param string $otroSotterShreniNiyomOnusongo
     */
    public function setOtroSotterShreniNiyomOnusongo($otroSotterShreniNiyomOnusongo)
    {
        $this->otroSotterShreniNiyomOnusongo = $otroSotterShreniNiyomOnusongo;
    }

    /**
     * @return string
     */
    public function getDharamotaNotePoriborton()
    {
        return $this->dharamotaNotePoriborton;
    }

    /**
     * @param string $dharamotaNotePoriborton
     */
    public function setDharamotaNotePoriborton($dharamotaNotePoriborton)
    {
        $this->dharamotaNotePoriborton = $dharamotaNotePoriborton;
    }

    /**
     * @return string
     */
    public function getDharamotaNotePoribortonMokaddomaNongAbongSon()
    {
        return $this->sanitize($this->dharamotaNotePoribortonMokaddomaNongAbongSon);
    }

    /**
     * @param string $dharamotaNotePoribortonMokaddomaNongAbongSon
     */
    public function setDharamotaNotePoribortonMokaddomaNongAbongSon($dharamotaNotePoribortonMokaddomaNongAbongSon)
    {
        $this->dharamotaNotePoribortonMokaddomaNongAbongSon = $dharamotaNotePoribortonMokaddomaNongAbongSon;
    }

    /**
     * @return string
     */
    public function getDharamotaNotePoribortonMokaddomaNongAbongSonDitiyoPata()
    {
        return $this->sanitize($this->dharamotaNotePoribortonMokaddomaNongAbongSonDitiyoPata);
    }

    /**
     * @param string $dharamotaNotePoribortonMokaddomaNongAbongSonDitiyoPata
     */
    public function setDharamotaNotePoribortonMokaddomaNongAbongSonDitiyoPata($dharamotaNotePoribortonMokaddomaNongAbongSonDitiyoPata)
    {
        $this->dharamotaNotePoribortonMokaddomaNongAbongSonDitiyoPata = $dharamotaNotePoribortonMokaddomaNongAbongSonDitiyoPata;
    }

    /**
     * @return string
     */
    public function getMantobboDitiyoPata()
    {
        return $this->sanitize($this->mantobboDitiyoPata);
    }

    /**
     * @param string $mantobboDitiyoPata
     */
    public function setMantobboDitiyoPata($mantobboDitiyoPata)
    {
        $this->mantobboDitiyoPata = $mantobboDitiyoPata;
    }

    /**
     * @return string
     */
    public function getNijDakholiyoJomirMotPorimanAkor()
    {
        return $this->sanitize($this->nijDakholiyoJomirMotPorimanAkor);
    }

    /**
     * @param string $nijDakholiyoJomirMotPorimanAkor
     */
    public function setNijDakholiyoJomirMotPorimanAkor($nijDakholiyoJomirMotPorimanAkor)
    {
        $this->nijDakholiyoJomirMotPorimanAkor = $nijDakholiyoJomirMotPorimanAkor;
    }

    /**
     * @return string
     */
    public function getNijDakholiyoJomirMotPorimanShotangsho()
    {
        return $this->sanitize($this->nijDakholiyoJomirMotPorimanShotangsho);
    }

    /**
     * @param string $nijDakholiyoJomirMotPorimanShotangsho
     */
    public function setNijDakholiyoJomirMotPorimanShotangsho($nijDakholiyoJomirMotPorimanShotangsho)
    {
        $this->nijDakholiyoJomirMotPorimanShotangsho = $nijDakholiyoJomirMotPorimanShotangsho;
    }

    /**
     * @return string
     */
    public function getOdhinosthoSotterKhajanaPrapokerKhatianNumber()
    {
        return $this->sanitize($this->odhinosthoSotterKhajanaPrapokerKhatianNumber);
    }

    /**
     * @param string $odhinosthoSotterKhajanaPrapokerKhatianNumber
     */
    public function setOdhinosthoSotterKhajanaPrapokerKhatianNumber($odhinosthoSotterKhajanaPrapokerKhatianNumber)
    {
        $this->odhinosthoSotterKhajanaPrapokerKhatianNumber = $odhinosthoSotterKhajanaPrapokerKhatianNumber;
    }

    /**
     * @return string
     */
    public function getOdhinosthoSotterBibhinnoKhatianerNumber()
    {
        return $this->sanitize($this->odhinosthoSotterBibhinnoKhatianerNumber);
    }

    /**
     * @param string $odhinosthoSotterBibhinnoKhatianerNumber
     */
    public function setOdhinosthoSotterBibhinnoKhatianerNumber($odhinosthoSotterBibhinnoKhatianerNumber)
    {
        $this->odhinosthoSotterBibhinnoKhatianerNumber = $odhinosthoSotterBibhinnoKhatianerNumber;
    }

    /**
     * @return string
     */
    public function getOdhinosthoSotterMotPorimanAkor()
    {
        return $this->sanitize($this->odhinosthoSotterMotPorimanAkor);
    }

    /**
     * @param string $odhinosthoSotterMotPorimanAkor
     */
    public function setOdhinosthoSotterMotPorimanAkor($odhinosthoSotterMotPorimanAkor)
    {
        $this->odhinosthoSotterMotPorimanAkor = $odhinosthoSotterMotPorimanAkor;
    }

    /**
     * @return string
     */
    public function getOdhinosthoSotterMotPorimanShotangsho()
    {
        return $this->sanitize($this->odhinosthoSotterMotPorimanShotangsho);
    }

    /**
     * @param string $odhinosthoSotterMotPorimanShotangsho
     */
    public function setOdhinosthoSotterMotPorimanShotangsho($odhinosthoSotterMotPorimanShotangsho)
    {
        $this->odhinosthoSotterMotPorimanShotangsho = $odhinosthoSotterMotPorimanShotangsho;
    }

    /**
     * @return string
     */
    public function getSorboMotShotok()
    {
        return $this->sanitize($this->sorboMotShotok);
    }

    /**
     * @param string $sorboMotShotok
     */
    public function setSorboMotShotok($sorboMotShotok)
    {
        $this->sorboMotShotok = $sorboMotShotok;
    }

    /**
     * @return string
     */
    public function getSorboMotAkor()
    {
        return $this->sanitize($this->sorboMotAkor);
    }

    /**
     * @param string $sorboMotAkor
     */
    public function setSorboMotAkor($sorboMotAkor)
    {
        $this->sorboMotAkor = $sorboMotAkor;
    }

    /**
     * @return string
     */
    public function getUttorSimanerDagerDakholkar()
    {
        return $this->sanitize($this->uttorSimanerDagerDakholkar);
    }

    /**
     * @param string $uttorSimanerDagerDakholkar
     */
    public function setUttorSimanerDagerDakholkar($uttorSimanerDagerDakholkar)
    {
        $this->uttorSimanerDagerDakholkar = $uttorSimanerDagerDakholkar;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return KhatianCorrectionLog
     */
    public function getCorrectionLog()
    {
        return $this->correctionLog;
    }

    public function setentryComplete($bool)
    {
        return $this->entryComplete = $bool;
    }

    public function isEntryComplete()
    {
        return $this->entryComplete;
    }
}