<div ui-layout options="{flow: 'column'}">
    <div id='col1' size="<?= @$col1['size'] . @$col1['sizetype'] ?>">
        <div class="container-fluid"><?= @$col1['content'] ?></div>
    </div>
    <div id='col2' size="<?= @$col2['size'] . @$col2['sizetype']?>">
        <div class="container-fluid"><?= @$col2['content'] ?></div>
    </div>
</div>