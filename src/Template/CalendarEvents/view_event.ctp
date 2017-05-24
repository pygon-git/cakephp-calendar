<div class="row">
    <div class="col-xs-4 col-md-2 text-right">
        <strong>Title:</strong>
    </div>
    <div class="col-xs-8 col-md-4">
        <?= $calEvent->title; ?>
    </div>
    <div class="col-xs-4 col-md-2 text-right">
        <strong>Start Date:</strong>
    </div>
    <div class="col-xs-8 col-md-4">
        <?= $calEvent->start_date->format('Y-m-d H:i:s'); ?>
    </div>
    <div class="col-xs-4 col-md-2 text-right">
        <strong>End Date:</strong>
    </div>
    <div class="col-xs-8 col-md-4">
        <?= $calEvent->end_date->format('Y-m-d H:i:s'); ?>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-xs-4 col-md-2 text-right">
        <strong>Content:</strong>
    </div>
    <div class="col-xs-8 col-md-4">
        <?= $calEvent->content;?>
    </div>
</div>
