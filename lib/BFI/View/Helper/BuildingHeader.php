<?php

namespace BFI\View\Helper;

use AQA\Gebaeude;
use AQA\Turm;
use AQA\Model\UserGebaeude;
use BFI\FrontController;
use BFI\View\Helper;

class BuildingHeader extends Helper
{
    /**
     * Map translator
     * @param string $key
     * @return string
     */
    protected function _($key)
    {
        return FrontController::getInstance()->getPlugin('translate')->_($key);
    }

    /**
     * Render the building header
     * @usage $this->buildingHeader($buildingObject, $buildingSpecialName, $buldingSpecialContent, $enoughResourcesTime);
     * @param array $params
     */
    public function render(array $params = array())
    {
        if (! $params[0] instanceof Gebaeude) {
            return;
        }
        $uri = new Uri();
        $button = new Button();
        $timer = new TimerFormat();
        $resourceList = new ResourceList();
        if (file_exists(BASE_PATH . '/public/js/gebaeude/' . $params[0]->abkuerzung . '.js')) : ?>
            <script language="JavaScript" type="text/javascript" src="/static-files/<?= $params[0]->abkuerzung; ?>.js/folder/gebaeude/"></script>
        <?php endif;?><div class="gebeaudebox abstand_u_10" id="interface">
            <a href="javascript:void(0);" class="schliessen" onclick="$j('#interface').toggle('fast');">X</a>
        <?php // Checkt ob Virus aktiv ist, bricht ab und macht Funktionen unmöglich


        if($params[0] instanceof Turm && $params[0]->deactivationTime > 0) {

            echo sprintf($this->_('text_towers_downtime'),$timer->render(array($params[0]->deactivationTime)));
            ?>
                <script type="text/javascript">addLLF(printTimer, <?= $params[0]->deactivationTime ?>, 'virentimer');</script>
            <?php
            return;
        }
        if($params[0]->isDestroyable()) {
            $button->render(array(array(
                'active'=>true,
                'class'=>'rechts abstand_o_0 abstand_r_10',
                'onClick' => 'bistDuSicher('. $this->_('bist_du_sicher') .', gebabreissen)',
                'content' => $this->_('gebaeude_abreissen')
            )));
        }
        ?>

        <h1 class="links"><?= $this->_('name_gebaeude_' . $params[0]->abkuerzung) . ' ' . $this->_('legend_stufe') . ' ' . $params[0]->arrGebaeude['stufe'] ?>/<?= UserGebaeude::$maxLvl[$params[0]->abkuerzung]; ?></h1>
            <a href="javascript:void(0);" class="button_gbd_beschreibung" onclick="$j('#gbd_beschreibung').toggle('fast');">?</a>
            <div id="gbd_beschreibung"><?= $this->_('gbd_beschreibung_' . $params[0]->abkuerzung); ?></div>

        <?php
        if($params[0]->isDisabled()) {
        ?>
        <br clear="all" /><b class="fehler">Deine Kuppel ist beschädigt!</b><br>Sinkt dein Kuppelzustand auf <b>
                <?php
                echo UserGebaeude::$minDomeCondition[$params[0]->abkuerzung];
                ?>
        %</b>, ist dieses Geb&auml;ude nicht mehr steuerbar und f&auml;llt komplett aus! Repariere den Schaden im Hauptquartier.

        <?php
         }
            if($params[0]->canBeBuilt()) {
                if($params[0]->intImBau === false) { ?>
                    <table class="gebaeude_info">
                        <tr>
                            <td width="0%" rowspan="3"><img src="/images/gebeaude/th_<?= $params[0]->abkuerzung?>.gif" alt="<?= $this->_('name_gebaeude_' . $params[0]->abkuerzung); ?>" align="absmiddle" /></td>
                            <td width="0%" class="rahmen_u fett"><?= $params[1]; ?>:</td>
                            <td width="100%" class="rahmen_u"><?= $params[2]; ?></td>
                        </tr>
                        <tr>
                            <td class="rahmen_u fett"><?= $this->_('legend_bauzeit'); ?>:</td>
                            <td class="rahmen_u"><?= $timer->render(array($params[0]->getEndzeit() - time()));?></td>
                        </tr>
                        <tr>
                            <td class="fett"><?= $this->_('legend_kosten'); ?>:</td>
                            <td><?= $resourceList->render(array($params[0]));?></td>
                        </tr>
                    </table>
                    <span class="fehler" id="fehler_ausbau"></span>
                    <?= $button->render(array($params[0]->ausbauButton())); ?>
                    <?php
                if (! is_null($params[3])) {
                if ($params[3] > 0) : ?>
                    <span class="textklein resources"><?= sprintf($this->_('enough_reources_time_label'), date("d.m.", $params[3]), date("H:i", $params[3])); ?></span>
                <?php else: ?>
                    <span class="textklein resources"><?= $this->_('enough_reources_never'); ?></span>';
                <?php endif;
                }
            } else { // wenn GEBAUT wird ?>
                <table class="gebaeude_info">
                    <tr>
                        <td width="0%" rowspan="3"><img src="/images/gebeaude/th_<?= $params[0]->abkuerzung; ?>.gif" alt="<?= $this->_('name_gebaeude_' . $params[0]->abkuerzung); ?>" align="absmiddle" /></td>
                        <td width="0%" class="rahmen_u fett"><?= $this->_('legend_ausbau'); ?>:</td>
                        <td width="100%" class="rahmen_u"><?= $this->_('legend_stufe'); ?> <?= $params[0]->arrGebaeude['stufe'] + 1; ?></td>
                    </tr>
                    <tr>
                        <td class="rahmen_u"><?= $this->_('legend_restdauer'); ?>:</td>
                        <td class="rahmen_u"><b id="restzeit"></b></td>
                    </tr>
                    <tr>
                        <td colspan="2" valign="top">
                            <?= $button->render(array(array(
                                'active' => true,
                                'style' => 'float: left;',
                                'content' => $this->_('button_bau_abbrechen'),
                                'href' => 'javascript:void(0)',
                                'onclick' => sprintf('return bistDuSicher(\'%s\',function(){\'%s\'});',
                                    sprintf($this->_('cancel_build_are_you_sure'), UserGebaeude::BAU_ABBRUCH_REST * 100),
                                    $uri->getAjaxLink($params[0]->abkuerzung, array(
                                        'abbruch' => 1,
                                        'position' => $params[0]->arrGebaeude['position']
                                    ))
                                )
                            ))); ?>
                        </td>
                    </tr>
                </table>
                <script type="text/javascript">addLLF(printTimer, <?= $params[0]->getTimer() ?>, 'restzeit');</script>
            <?
            }
        } ?>
        <br clear="all" /><?php
    }
}