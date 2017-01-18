<input type="hidden" id="nav_id" name="nav_id" value="edit" placeholder="">

<div class="frame">
    <div class="alert alert-success"><?= $process_operation ?>結果は下記内容をご確認ください</div>

    <? if(isset($process_result_cnt["OK"])){ ?>
        <h5 class="control-label">
            <span class="label label-success"><?= $process_operation ?>OK件数：<?= $process_result_cnt["OK"]; ?></span>
            <span class="label label-warning"><?= $process_operation ?>NG件数：<?= $process_result_cnt["NG"]; ?></span>
        </h5>
    <? } ?>
    <? if(isset($ret_rule_data_list)) { ?>
    <table id="userlist_table_rule" class="display hide">
        <thead>
            <tr>
            <? foreach ($header_list_complete_rule as $header) { ?>
                <th><?= $header ?></th>
            <? } ?>
            </tr>
        </thead>
        <tbody>
        <? foreach ($ret_rule_data_list as $account_id => $rule_data_list) { ?>
            <? foreach ($rule_data_list as $rule_data) { ?>
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
                        <td><?= $process_operation.$data["result_status"] ?></td>
                        <td><?= $data["result_message"] ?></td>
                        <td><?= $data["result_detail"] ?></td>
                    </tr>
                <? } ?>
            <? } ?>
        <? } ?>
        </tbody>
    </table>
    <? } ?>
    <? if(isset($ret_combi_data_list)) { ?>
    <table id="userlist_table_combination" class="display hide">
        <thead>
            <tr>
            <? foreach ($header_list_complete_combination as $header) { ?>
                <th><?= $header ?></th>
            <? } ?>
            </tr>
        </thead>
        <tbody>
        <? foreach ($ret_combi_data_list as $account_id => $combination_data_list) { ?>
            <? foreach ($combination_data_list as $combination_data) { ?>
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
                        <td><?= $process_operation.$data["result_status"] ?></td>
                        <td><?= $data["result_message"] ?></td>
                        <td><?= $data["result_detail"] ?></td>
                    </tr>
                <? } ?>
            <? } ?>
        <? } ?>
        </tbody>
    </table>
    <? } ?>
    <? if(isset($ret_gdn_rule_data_list)) { ?>
    <table id="userlist_table_gdn_rule" class="display hide">
        <thead>
            <tr>
            <? foreach ($header_list_gdn_complete_rule as $header) { ?>
                <th><?= $header ?></th>
            <? } ?>
            </tr>
        </thead>
        <tbody>
        <? foreach ($ret_gdn_rule_data_list as $account_id => $rule_data_list) { ?>
            <? foreach ($rule_data_list as $rule_data) { ?>
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
                        <td><?= $process_operation.$data["result_status"] ?></td>
                        <td><?= $data["result_message"] ?></td>
                        <td><?= $data["result_detail"] ?></td>
                    </tr>
                <? } ?>
            <? } ?>
        <? } ?>
        </tbody>
    </table>
    <? } ?>
</div>

<?= Asset::js('userlist/complete.js') ?>