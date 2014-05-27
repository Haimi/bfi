<?php

namespace BFI\View\Helper;

use AQA\Exception;
use AQA\Gebaeude;
use AQA\Model\AqaRf;
use AQA\Model\AqaTechnologien;
use AQA\Model\AqaKrieger;
use AQA\Model\AqaKuppeln;
use AQA\Model\AqaTransporter;
use AQA\Model\UserGebaeude;
use AQA\Model\Lager;
use BFI\FrontController;
use BFI\Plugin\Translate;
use BFI\View\Helper;

/**
 * Class ResourceList
 * @package BFI\View\Helper
 */
class ResourceList extends Helper
{
    /**
     * Alternativdarstellung
     */
    const DISPLAY_BUILDING = 0;
    const DISPLAY_REPAIR = 1;
    const DISPLAY_TRANSPORTER = 2;
    const DISPLAY_DEN = 3;

    /**
     * Alle erlaubten Darstellungen
     * @var array
     */
    public static $allowedDisplays = array(
        self::DISPLAY_BUILDING,
        self::DISPLAY_REPAIR,
        self::DISPLAY_TRANSPORTER,
        self::DISPLAY_DEN
    );

    /**
     * @var Gebaeude
     */
    protected $_building = null;

    /**
     * @var \AQA\User
     */
    protected $_currentUser = null;

    /**
     * @var Translate
     */
    protected $_trans = null;

    /**
     * @var int
     */
    protected $_display = null;

    /**
     * C'tor
     */
    public function __construct()
    {
        $this->_currentUser = FrontController::getInstance()->getPlugin('user')->getCurrentUser();
        $this->_trans = FrontController::getInstance()->getPlugin('translate');
    }

    /**
     * Render needed resources
     * @param array $params
     * @return string
     * @throws \AQA\Exception
     */
    public function render(array $params = array())
    {
        if ($params[0] instanceof Gebaeude) {
            $this->_building = $params[0];
            $this->_display = self::DISPLAY_BUILDING;
        } else {
            if (in_array($params[0], self::$allowedDisplays)) {
                $this->_display = $params[0];
            } else {
                throw new Exception(__CLASS__ . ' needs an instance of \Aqa\Gebaeude');
            }
        }

        $arrLager = Lager::getInstance()->getStorage($this->_currentUser->id, $this->_currentUser->currentKuppel);

        // Verfügbare Energie
        $intRestEnergie = UserGebaeude::getInstance()->getEnergyAvailable(
            $this->_currentUser->id,
            $this->_currentUser->currentKuppel
        );

        $intSauerstoffGesamt = AqaTechnologien::getInstance()->getGesamtSauerstoffCached(
            $this->_currentUser->id,
            $this->_currentUser->currentKuppel
        );
        $intSauerstoffVerbrauch = AqaKrieger::getInstance()->getSauerstoffverbrauchCached(
            $this->_currentUser->id,
            $this->_currentUser->currentKuppel
        );
        $intRestSauerstoff = ($intSauerstoffGesamt-$intSauerstoffVerbrauch);

        $arrKosten = array();
        if ($this->_display === self::DISPLAY_BUILDING) {
            $arrKosten = UserGebaeude::$cost[$this->_building->abkuerzung][$this->_building->arrGebaeude['stufe']];
            $arrKosten[UserGebaeude::RES_ENERGY] = UserGebaeude::$energyPerLevel[$this->_building->abkuerzung];
        } else {
            switch ($this->_display) {
                case self::DISPLAY_REPAIR:
                    $arrKosten = AqaKuppeln::getInstance()->getRepairCostCached(
                        $this->_currentUser->id,
                        $this->_currentUser->currentKuppel
                    );
                    break;
                case self::DISPLAY_TRANSPORTER:
                    $arrKosten = AqaTransporter::getInstance()->getTransporterCostCached(
                        AqaTransporter::TYPE_TRANSPORT_DEMANTOIDREACTOR,
                        $this->_currentUser->id,
                        $this->_currentUser->currentKuppel
                    );
                    break;
                case self::DISPLAY_DEN:
                    $arrKosten = $params[1];
                    break;
            }
        }

        $strReturn = '';
        // Erfahrung
        if (array_key_exists(AqaTechnologien::TECH_EXPERIENCE_POINTS, $arrKosten) &&
            $arrKosten[AqaTechnologien::TECH_EXPERIENCE_POINTS] > 0) {
            $strReturn .= '<img align="absmiddle" title="' . $this->_trans->_('legend_erfahrung') . '" alt="' . $this->_trans->_('legend_erfahrung') . '" src="/images/icons/erfahrung.png"/> ';
            $strReturn .= '<span class="weiss">' . $arrKosten[AqaTechnologien::TECH_EXPERIENCE_POINTS] . '</span>';
            $strReturn .= '&nbsp;&nbsp;&nbsp;';
        }

        // Stahl ausgeben
        if (array_key_exists(AqaRf::RES_STAHL, $arrKosten) &&
            $arrKosten[AqaRf::RES_STAHL] > 0) {
            $strReturn .= '<img align="absmiddle" title="' . $this->_trans->_('legend_st') . '" alt="' . $this->_trans->_('legend_st') . '" src="/images/icons/stahl.png"/> ';
            $strReturn .= '<span ' . ($arrLager[AqaRf::RES_STAHL] >= $arrKosten[AqaRf::RES_STAHL] ? '' : 'class="rot"') . '>' . $arrKosten[AqaRf::RES_STAHL] . '</span>';
            $strReturn .='&nbsp;&nbsp;&nbsp;';
        }

        // Fluorid
        if (array_key_exists(AqaRf::RES_FLUORID, $arrKosten) &&
            $arrKosten[AqaRf::RES_FLUORID] > 0) {
            $strReturn .= '<img align="absmiddle" title="' . $this->_trans->_('legend_fl') . '" alt="' . $this->_trans->_('legend_fl') . '" src="/images/icons/fluorid.png"/> ';
            $strReturn .= '<span ' . ($arrLager[AqaRf::RES_FLUORID] >= $arrKosten[AqaRf::RES_FLUORID] ? '' : 'class="rot"') . '>' . $arrKosten[AqaRf::RES_FLUORID] . '</span>';
            $strReturn .='&nbsp;&nbsp;&nbsp;';
        }

        // Toberit
        if (array_key_exists(AqaRf::RES_TOBERIT, $arrKosten) &&
            $arrKosten[AqaRf::RES_TOBERIT] > 0) {
            $strReturn .= '<img align="absmiddle" title="' . $this->_trans->_('legend_to') . '" alt="' . $this->_trans->_('legend_to') . '" src="/images/icons/toberit.png"/> ';
            $strReturn .= '<span ' . ($arrLager[AqaRf::RES_TOBERIT] >= $arrKosten[AqaRf::RES_TOBERIT] ? '' : 'class="rot"') . '>' . $arrKosten[AqaRf::RES_TOBERIT] . '</span>';
            $strReturn .='&nbsp;&nbsp;&nbsp;';
        }

        // Demantoid
        if (array_key_exists(AqaRf::RES_DEMANTOID, $arrKosten) &&
            $arrKosten[AqaRf::RES_DEMANTOID] > 0) {
            $strReturn .= '<img align="absmiddle" title="' . $this->_trans->_('legend_de') . '" alt="' . $this->_trans->_('legend_de') . '" src="/images/icons/demantoid.png"/> ';
            $strReturn .= '<span ' . ($arrLager[AqaRf::RES_DEMANTOID] >= $arrKosten[AqaRf::RES_DEMANTOID] ? '' : 'class="rot"') . '>' . $arrKosten[AqaRf::RES_DEMANTOID] . '</span>';
            $strReturn .='&nbsp;&nbsp;&nbsp;';
        }

        // Energie
        if (array_key_exists(UserGebaeude::RES_ENERGY, $arrKosten) &&
            $arrKosten[UserGebaeude::RES_ENERGY] > 0) {
            $strReturn .= '&nbsp;&nbsp;&nbsp;';
            $strReturn .= '<img align="absmiddle" title="' . $this->_trans->_('legend_en') . '" alt="' . $this->_trans->_('legend_en') . '" src="/images/icons/energie.png"/> ';
            $strReturn .= '<span class="' . ($intRestEnergie >= $arrKosten[UserGebaeude::RES_ENERGY] ? 'weiss':'rot') . '">' . $arrKosten[UserGebaeude::RES_ENERGY] . '</span> <span class="blau">/</span> ' . $this->_trans->_('legend_stufe');
        }

        // Sauerstoff
        if (array_key_exists(UserGebaeude::RES_OXYGEN, $arrKosten) &&
            $arrKosten[UserGebaeude::RES_OXYGEN] > 0) {
            $strReturn .= '&nbsp;&nbsp;&nbsp;';
            $strReturn .= '<img align="absmiddle" title="' . $this->_trans->_('legend_sa') . '" alt="' . $this->_trans->_('legend_sa') . '" src="/images/icons/sauerstoff.png"/> ';
            $strReturn .= '<span class="' . ($intRestSauerstoff >= $arrKosten[UserGebaeude::RES_OXYGEN] ? 'weiss' : 'rot') . '">' . $arrKosten[UserGebaeude::RES_OXYGEN] . '</span>';
        }
        return $strReturn;
    }
}