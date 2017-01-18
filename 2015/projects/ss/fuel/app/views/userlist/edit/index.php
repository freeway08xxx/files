<div class="legacy">
    <input type="hidden" id="nav_id" name="nav_id" value="edit" placeholder="">

    <form method="post" name="form_confirm" enctype="multipart/form-data">
        <div class="clearfix list-upload">
            <fieldset class="columns">
                <legend>ユーザリストデータの取得</legend>
                    <input id="user_list_file" class="form-input" name="user_list_file" type="file"/>
            </fieldset>
            <fieldset class="columns well well-sm">
                <legend>登録用フォーマットはこちら</legend>
                <a href="<?= $ydn_rule_format_file ?>">
                    <img src="/sem/new/assets/img/excel.gif" border="0"> YDNルール.xlsx
                </a></br>
                <a href="<?= $ydn_combi_format_file ?>">
                    <img src="/sem/new/assets/img/excel.gif" border="0"> YDN組み合わせ.xlsx
                </a></br>
                <a href="<?= $google_rule_format_file ?>">
                    <img src="/sem/new/assets/img/excel.gif" border="0"> Googleルール.xlsx
                </a></br>
                <a href="<?= $google_combi_format_file ?>">
                    <img src="/sem/new/assets/img/excel.gif" border="0"> Google組み合わせ.xlsx
                </a></br>
            </fieldset>
        </div>
    </form>
    <? if(isset($uniqid)) { ?>
        <div class="clearfix btn-area">
            <form method="post" name="form_edit">
                <button type="button" class="btn btn-primary js-href-canceled" id="api_regist">
                    下記内容で<?= $process_operation ?>する
                </button>
                <input type="hidden" name="uniqid" value="<?= $uniqid; ?>" />
                <input type="hidden" name="upload_filename" value="<?= $upload_filename; ?>" />
                <? if(isset($rule_data_cnt)) { ?>
                <input type="hidden" name="rule_data_cnt" value="<?= $rule_data_cnt; ?>" />
                <? } ?>
                <? if(isset($combination_data_cnt)) { ?>
                <input type="hidden" name="combination_data_cnt" value="<?= $combination_data_cnt; ?>" />
                <? } ?>
            </form>
        </div>
    <? } ?>

    <div class="frame">
        <? if(isset($ret_ydn_rule_data_list)) { ?>
            <table id="userlist_table_rule" class="display hide">
                <thead>
                    <tr>
                    <? foreach ($header_list_regist_rule as $header) { ?>
                        <th><?= $header ?></th>
                    <? } ?>
                    </tr>
                </thead>
                <tbody>
                    <? foreach ($ret_ydn_rule_data_list as $account_id => $rule_data) { ?>
                        <? foreach ($rule_data as $data) { ?>
                        <tr>
                            <td><?= $data["account_id"] ?></td>
                            <td><?= $data["list_name"] ?></td>
                            <td><?= $ydn_open_flg[$data["open"]] ?></td>
                            <td><?= $data["reach_period"] ?></td>
                            <td><?= $ydn_preset_flg[$data["preset"]] ?></td>
                            <td><?= $data["set_condition"] ?></td>
                            <? if($data["group"]) { ?>
                                <? foreach ($data["group"] as $group_no => $group_data_list) { ?>
                                    <? foreach ($group_data_list as $param) { ?>
                                    <td><?= $group_no ?></td>
                                    <td><?= $ydn_rule_type[$param["type"]] ?></td>
                                    <td><?= $ydn_rule_condition[$param["compareOperator"]] ?></td>
                                    <td><?= $param["value"] ?></td>
                                    <? } ?>
                                <? } ?>
                            <? } ?>
                            <td><?= $data["description"] ?></td>
                            <td><?= $data["tagid"] ?></td>
                        </tr>
                        <? } ?>
                    <? } ?>
                </tbody>
            </table>
        <? } ?>
        <? if(isset($ret_gdn_rule_data_list)) { ?>
            <table id="userlist_table_gdn_rule" class="display hide">
                <thead>
                    <tr>
                    <? foreach ($header_list_regist_gdn_rule as $header) { ?>
                        <th><?= $header ?></th>
                    <? } ?>
                    </tr>
                </thead>
                <tbody>
                    <? foreach ($ret_gdn_rule_data_list as $account_id => $rule_data) { ?>
                        <? foreach ($rule_data as $data) { ?>
                        <tr>
                            <td><?= $data["account_id"] ?></td>
                            <td><?= $data["list_name"] ?></td>
                            <td><?= $data["reach_period"] ?></td>
                            <? if($data["group"]) { ?>
                                <? foreach ($data["group"] as $group_no => $group_data_list) { ?>
                                    <? foreach ($group_data_list as $param) { ?>
                                    <td><?= $group_no ?></td>
                                    <td><?= $gdn_rule_type[$param["type"]] ?></td>
                                    <td><?= $gdn_rule_condition[$param["compareOperator"]] ?></td>
                                    <td><?= $param["value"] ?></td>
                                    <? } ?>
                                <? } ?>
                            <? } ?>
                            <td><?= $data["description"] ?></td>
                        </tr>
                        <? } ?>
                    <? } ?>
                </tbody>
            </table>
        <? } ?>
        <? if(isset($ret_combi_data_list)) { ?>
            <table id="userlist_table_combination" class="display hide">
                <thead>
                    <tr>
                    <? foreach ($header_list_regist_combination as $header) { ?>
                        <th><?= $header ?></th>
                    <? } ?>
                    </tr>
                </thead>
                <tbody>
                    <? foreach ($ret_combi_data_list as $account_id => $combination_data) { ?>
                        <? foreach ($combination_data as $data) { ?>
                        <tr>
                            <td><?= $data["account_id"] ?></td>
                            <td><?= $data["list_name"] ?></td>
                            <? if($data["group"]) { ?>
                                <? foreach ($data["group"] as $group_no => $group_data_list) { ?>
                                    <? foreach ($group_data_list as $param) { ?>
                                        <td><?= $group_no ?></td>
                                        <td><?= $param["logical_operator"] ?></td>
                                        <td><?= $param["target_list_name"] ?></td>
                                    <? } ?>
                                <? } ?>
                            <? } ?>
                            <td><?= $data["description"] ?></td>
                        </tr>
                        <? } ?>
                    <? } ?>
                </tbody>
            </table>
        <? } ?>
    </div>

</div>

<?= Asset::js('userlist/edit.js') ?>