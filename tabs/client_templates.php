<div class="sms_net_bd_wrapper">
    <div class="tabbed_section">
        <?php include_once SMS_NET_BD_PATH . '/partials/nav.php'; ?>
        <div class="tabbed_section_content_wrap">
            <div class="tabbed_section_content tabbed_section_content_active">

                <div class="custom-container">

                    <?php

                    if ($_POST['submit']) {

                        try {

                            $where = ['user_type' => 'client'];
                            $result = select_query("sms_net_bd_templates", "*", $where);


                            while ($data = mysql_fetch_array($result)) {

                                if ($_POST[$data['id'] . '_active'] == "on") {
                                    $tmp_active = 1;
                                } else {
                                    $tmp_active = 0;
                                }

                                if (isset($_POST[$data['id'] . '_extra'])) {
                                    $extra_info = trim($_POST[$data['id'] . '_extra']);
                                } else {
                                    $extra_info = '';
                                }

                                $update = [
                                    "content" => $_POST[$data['id'] . '_template'],
                                    "is_active" => $tmp_active,
                                    "extra_info" => $extra_info
                                ];

                                update_query("sms_net_bd_templates", $update, "id = " . $data['id']);
                            }

                            showAlert("Saved Successfully", "success");
                        } catch (\Throwable $th) {

                            showAlert($th->getMessage(), "danger");
                        }
                    }

                    ?>


                    <form method="POST" action="">

                        <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
                            <tbody>

                                <tr>
                                    <td colspan="2">
                                    </td>
                                </tr>

                                <?php
                                $where = ['user_type' => 'client'];
                                $templates = select_query("sms_net_bd_templates", "*", $where);
                                ?>

                                <?php while ($template = mysql_fetch_array($templates)) : ?>

                                    <?php

                                    if ($template['is_active'] == 1) {
                                        $active   = 'checked = "checked"';
                                        $disabled = '';
                                    } else {
                                        $active   = '';
                                        $disabled = 'readonly=true';
                                    }

                                    ?>


                                    <tr>
                                        <td class="fieldlabel" style="float:right;">Parameter(s)</td>
                                        <td class="<?php echo '' . $template['id'] . '_link' ?>">

                                            <?php
                                            $variables = explode(",", $template['variables']);

                                            $var_count = count($variables);

                                            $i = 0;

                                            foreach (explode(",", $template['variables']) as $token) {
                                                $i++;
                                                echo '<a href="javascript:void(0)" id="' . $token . '" class="font12 left setalink" data-token="' . $token . '" onclick="insertToken(this)">' . ucfirst(preg_replace("/[{}]/", "", $token)) . '</a>';
                                                if ($i != $var_count) {
                                                    echo ' | ';
                                                }
                                            }

                                            ?></td>
                                    </tr>

                                    <tr>
                                        <td class="fieldlabel" width="30%"><input type="checkbox" value="on" id="<?= $template['id']; ?>_checkbox" name="<?= $template['id']; ?>_active" <?= $active ?> onchange="togglecheckbox(this)"><label for="<?= $template['id']; ?>_checkbox"> <?= formatHookName($template['name']); ?></label></td>
                                        <td class="fieldarea">
                                            <textarea name="<?= $template['id']; ?>_template" class="form-control" <?= $disabled ?> id="<?= $template['id']; ?>_check"><?= $template['content']; ?></textarea>
                                            <span><?= $template['description']; ?></span>
                                        </td>
                                    </tr>

                                    <?php if (!empty($template['extra_info'])) : ?>
                                        <tr>
                                            <td class="fieldlabel" width="30%"> Number of days (x)</td>
                                            <td class="fieldarea">
                                                <input type="number" name="<?= $template['id']; ?>_extra" value="<?= $template['extra_info']; ?>">
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <tr>
                                        <td colspan="2">
                                            <hr>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>



                            </tbody>
                        </table>

                        <div class="btn-container">
                            <input type="submit" value="Save Changes" class="btn btn-primary" tabindex="52" name="submit">
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function insertToken(obj) {
        var dataToken = $(obj).attr('data-token');
        var id = parseInt($(obj).parent('td').attr('class').split('_')[0], 10);
        insertAtCaret(dataToken, id);

    }

    function insertAtCaret(textFeildValue, id) {
        var textObj = document.getElementById("" + id + "_check");
        if (document.all) {
            if (textObj.createTextRange && textObj.caretPos) {
                var caretPos = textObj.caretPos;
                caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? textFeildValue + ' ' : textFeildValue;
            } else {
                textObj.value = textObj.value + textFeildValue;
            }
        } else {
            if (textObj.setSelectionRange) {
                var rangeStart = textObj.selectionStart;
                var rangeEnd = textObj.selectionEnd;
                var tempStr1 = textObj.value.substring(0, rangeStart);
                var tempStr2 = textObj.value.substring(rangeEnd);

                textObj.value = tempStr1 + textFeildValue + tempStr2;
            } else {
                alert("This version of Mozilla based browser does not support setSelectionRange");
            }
        }
    }
</script>