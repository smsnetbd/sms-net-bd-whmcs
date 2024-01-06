<div class="sms_net_bd_wrapper">
    <div class="tabbed_section">
        <?php include_once SMS_NET_BD_PATH . '/partials/nav.php'; ?>
        <div class="tabbed_section_content_wrap">
            <div class="tabbed_section_content tabbed_section_content_active">

                <div class="custom-container">

                    <?php

                    if ($_POST['submit']) {

                        try {
                            $class = new SmsClass();
                            $where = ['user_type' => 'admin'];
                            $result = select_query("sms_net_bd_templates", "*", $where);

                            $errorMessages = [];

                            while ($data = mysql_fetch_array($result)) {

                                if ($_POST[$data['id'] . '_active'] == "on") {
                                    $tmp_active = 1;
                                } else {
                                    $tmp_active = 0;
                                }

                                if (isset($_POST[$data['id'] . '_extra'])) {
                                    $update['extra_info'] = trim($_POST[$data['id'] . '_extra']);
                                }

                                $update = [
                                    "content" => $_POST[$data['id'] . '_template'],
                                    "is_active" => $tmp_active,
                                    "admin_numbers" => $_POST[$data['id'] . '_admin_numbers']
                                ];

                                $admin_numbers = explode(",", $update['admin_numbers']);
                                $invalidNumbers = [];

                                $error = false;

                                foreach ($admin_numbers as $gsm) {
                                    if (!empty($gsm)) {
                                        if (!$class->validatePhoneNumber($gsm)) {
                                            $hook_name = $data['name'];
                                            $invalidNumbers[] = $gsm;
                                            $errorMessage = "Invalid Admin Number ( " . implode(',', $invalidNumbers) . " ) for " . $hook_name;
                                            $errorMessages[$hook_name] = $errorMessage;
                                            $error = true;
                                        }
                                    }
                                }
                                //update query if no error
                                if ($error == false) {
                                    update_query("sms_net_bd_templates", $update, "id = '" . $data['id'] . "'");
                                }
                            }

                            if (empty($errorMessages)) {

                                showAlert("Saved Successfully", "success");
                            } else {

                                foreach ($errorMessages as $errorMessage) {
                                    showAlert($errorMessage, "danger");
                                }
                            }
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
                                $where = ['user_type' => 'admin'];
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

                                    <tr>
                                    <tr>
                                        <td class="fieldlabel" width="30%"> Admin Numbers </td>
                                        <td class="fieldarea">
                                            <input type="text" class="form-control" name="<?= $template['id']; ?>_admin_numbers" value="<?= $template['admin_numbers']; ?>">
                                        </td>
                                    </tr>
                                    </tr>


                                    <tr>
                                        <td colspan="2">
                                            <hr>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>



                            </tbody>
                        </table>

                        <div class="btn-container">
                            <input type="submit" value="Save" class="btn btn-primary" tabindex="52" name="submit">
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