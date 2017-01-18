<link rel="stylesheet" media="screen,print" type="text/css" href="/sem/keyword_monitor/css/basis.css"/>
<link rel="stylesheet" media="screen,print" type="text/css" href="/sem/keyword_monitor/css/moorabar.css"/>
<script type="text/javascript" src="/sem/keyword_monitor/js/jquery-1.3.2.js"></script>
<? if ($errors){ ?>
  <ul>
  <? foreach($errors as $error) { ?>
    <li>
      <?=$error;?>
    </li>
  <? } ?>
  </ul>
<? } ?>
  <?= $table;?>
</div>