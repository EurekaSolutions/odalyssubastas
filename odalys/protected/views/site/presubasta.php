<?php //Yii::app()->clientScript->registerCoreScript('jquery');
/* @var $this SiteController */

$this->pageTitle = Yii::app()->name;

$idsub = $presubasta->imagen_s_id . '_' . uniqid();

// Desactivamos para evitar conflicto con el .dialog del modal.
Yii::app()->clientscript->scriptMap = array('jquery-ui.min.js' => false,
    'jquery-ui.js' => false,
    'jquery.min.js' => false,
    'jquery.js' => false,);

$imagenesDir = 'http://www.odalys.com/odalys/';

?>
<style type="text/css">
    .global {

        text-align: center;
    }
    .global input, select{
        width: 100%;
    }
</style>
<table>
    <tr>
        <td style='width: 50%;'>


            <?php
            $imagen = $presubasta->imagenS;

            if ($subasta['fuesilenciosa']) {
                echo '<div id="imageng_' . $presubasta->imagen_s_id . ' class="image">
                                                                    <table>
                                                                    <td  style="vertical-align:top"><img src="' . $imagenesDir . $imagen['imagen'] . '"/></td>
                                                                    <td style="padding-left:14px">
                                                                        <p>' . $imagen['descri'] . '</p>
                                                                        <BR/>
                                                                        <precio id="' . $presubasta->imagen_s_id . '">';

                //Verificando si es primera puja
                $imgConPujas = RegistroPujas::model()->find('id_imagen_s=:imagen',
                    array(
                        ':imagen' => $presubasta->imagen_s_id,
                    ));

                // Usuario activo que tiene paleta y codigo asignado en esta subasta o es administrador
                $usuario_activo = Yii::app()->session['admin'] ||
                    (Yii::app()->session['id_usuario'] &&
                        Usuariospujas::model()->find(' idsubasta=:idsubasta AND idusuario=:idusuario', array(':idsubasta' => $subasta->id, ':idusuario' => Yii::app()->session['id_usuario'])));
                if ($usuario_activo) {
                    echo 'Precio actual: <div><moneda>' . $subasta->moneda . '</moneda> <actual_' . $presubasta->imagen_s_id . '>' . number_format($imagen['actual']) . '</actual_' . $presubasta->imagen_s_id . '><BR></div>
                                                                     Minimo puja maxima: <div><moneda>' . $subasta->moneda . '</moneda> <siguiente_' . $presubasta->imagen_s_id . '>';


                    if ($imgConPujas)
                        echo number_format($imagen['actual'] * 1.1);
                    else
                        echo number_format($imagen['base']);
                }

                echo '</siguiente_' . $presubasta->imagen_s_id . '></div>
                                                                        </precio><BR> </td></table>
                                                              </div>';
            } else
                throw new Exception("Error Processing Request: imagen no pertenece a subasta silenciosa activa." . $presubasta->imagen_s_id, 1);
            ?>

        </td>
        <td style="padding-left:14px; width: 50%;">
            <div class="form">

                <?php $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'presubasta-form',
                    'enableClientValidation' => true,
                    'enableAjaxValidation' => false,
                    'focus' => array($presubasta, 'opcion'),
                )); ?>
                <div class="global">

                    <?php

                    $select = array($presubasta->opcion);
                    echo $form->dropDownList($presubasta, 'opcion',
                        array('0' => 'Dejar puja máxima', '1' => 'Dejar puja telefónica', '2' => 'Asistir a la subasta en vivo', '3' => 'No hacer nada'),
                        array(/*'empty' => '(Selecciona una opción)',*/
                            'id' => 'seleccion_opcion', 'onchange' => 'seleccionPresubasta(this.value);'));
                    ?>

                    <div class="row" id="monto" style="display: block;">
                        <?php //echo $form->labelEx($presubasta,'monto'); ?>
                        <?php echo $form->textField($presubasta, 'monto', array('size' => 60, 'maxlength' => 255, 'placeholder' => 'Monto',
                            'oninput' => 'js: var precio = 0;  if($(this).val() != ""){
             precio = $(this).val();}
             else{ precio = 0(); }
             $("#' . $idsub . '").attr("value","Enviar ' . $subasta->moneda . ' "+number_format(precio));
              ', 'id' => 'montoValor_' . $idsub)); ?>
                        <?php echo $form->error($presubasta, 'monto'); ?>
                    </div>

                    <div class="row">
                        <?php echo $form->hiddenField($presubasta, 'imagen_s_id', array(/*'value'=>$subasta->imagen*/)); ?>
                    </div>
                </div>


                <script type="application/javascript">

                    function seleccionPresubasta(opcion) {
                        if (opcion == 0)
                            $("#monto").show();
                        else {
                            $("#<?php echo $idsub; ?>").attr("value", "Enviar");
                            $("#montoValor_<?php echo $idsub ?>").val('');
                            $("#monto").hide();
                        }


                    }

                    /*    $(document).ready(function(){


                     $("#seleccion_opcion").change(function(){
                     //console.log("Hola");
                     var value = this.val();

                     if(value == 0)
                     $("#monto").show();
                     else
                     $("#monto").hide();

                     alert("DDL value"+value);
                     //statement
                     });

                     });*/

                </script>
                <div class="row buttons global">
                    <?php //echo CHtml::submitButton($presubasta->isNewRecord ? 'Dejar Puja' : 'Dejar Puja'); ?>
                    <?php

                    /*
                            Yii::app()->clientScript->registerScript('seleccion_opcionnombre','
                    $(document).ready(function(){
                                                                $("#seleccion_opcion").on("change",function(){
                                                                    var value = this.val();

                                                                    if(value == 0)
                                                                     $("#monto").show();
                                                                    else
                                                                     $("#monto").hide();

                                                                    alert("DDL value"+value);
                                                                    //statement
                                                                });
                    });
                                                                                    ',
                                CClientScript::POS_READY);*/


                    echo CHtml::ajaxSubmitButton('Enviar', CHtml::normalizeUrl(array('site/presubasta')),
                        array('type' => 'POST',//'update'=>'#pujaModal',
                            'dataType' => "json",
                            //'data' => '{imagen_ss: "0"}',
                            'error' => 'function(data){
													//alert("Error");
													//console.log(data);
													if(data["status"] == 200){
														$("#pujaModal").html(data["responseText"]);
													}
													else{
														alert(data["responseText"]);
													}
												}',
                            'success' => 'function(data){
													json = data;
														if(data[\'id\']){
															alert(data["msg"]);
															if(data["success"]){

																$("#pujaModal").dialog("close");
																//location.reload();

															}else
																$("#pujaModal").html(data["responseText"]);
														}else{
															$("#pujaModal").html(data);

														}

												}',
                            'context' => 'js:this',

                            'beforeSend' => 'function(xhr,settings){

										        }',
                            'complete' => 'function(){

										            }',
                        ),
                        array('class' => 'btn global', 'style' => 'width:100%;', 'id' => $idsub));


                    ?>
                </div>

                <?php $this->endWidget(); ?>

            </div>
            <!-- form -->

        </td>
    </tr>
</table>