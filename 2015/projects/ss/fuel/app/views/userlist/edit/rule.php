<thead>
    <tr>
        <? foreach ($header_list as $header) { ?>
            <th><?= $header ?></th>
        <? } ?>
    </tr>
</thead>
<tbody>
    <? if($rule_data_list) { ?>
        <? foreach ($rule_data_list as $data) { ?>
        <tr>
            <td><?= $data["account_id"] ?></td>
            <td><?= $data["list_id"] ?></td>
            <td><?= $data["name"] ?></td>
            <td><?= $data["status"] ?></td>
            <td><?= $data["membershipLifeSpan"] ?></td>
            <td><?= $data["size"] ?></td>
            <td><?= $data["type"] ?></td>
            <? for ($i=0; $i < 10; $i++) { ?>
                <? if(empty($data["condition_".$i])){ ?>
                    <td>-</td>
                <? }else{ ?>
                    <td><?= $data["condition_".$i] ?></td>
                <? } ?>
            <? } ?>
        </tr>
        <? } ?>
    <? } ?>
</tbody>
