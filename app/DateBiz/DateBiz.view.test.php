<h2>Test diff</h2>
<?php

$today = new DateBiz();

$dow = [ 'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

$da = [
    new DateBiz(date('Y-m-d')),
    new DateBiz(date('Y-m-01')),
    new DateBiz(date('2015-m-d')),
    new DateBiz(date('Y-01-01')),
    new DateBiz(date('Y-01-06')),
    new DateBiz(date('Y-01-07')),
    new DateBiz(date('Y-01-08')),
    new DateBiz(date('Y-04-30')),
    new DateBiz(date('Y-05-01')),
    new DateBiz(date('Y-05-02')),
    new DateBiz(date('Y-05-03')),
    new DateBiz(date('Y-05-04')),
    new DateBiz(date('Y-05-05')),
    new DateBiz(date('Y-05-06')),
    new DateBiz(date('Y-05-07')),
    new DateBiz(date('Y-05-08')),
    new DateBiz(date('Y-05-09')),
    new DateBiz(date('Y-05-10')),
    new DateBiz(date('Y-06-01')),
    new DateBiz(date('Y-08-01')),
    new DateBiz(date('Y-11-01')),
    new DateBiz(date('Y-12-31')),
];

echo p('Today is '.$today->format('Y-m-d (w D)'));
?>
<table class="table table-condensed table-striped table-responsive">
    <thead>
        <th>Date 1</th>
        <th>Date 2</th>
        <th>Calendar days</th>
        <th>Working weekdays</th>
        <th>Adjusted business days</th>
    </thead>
    <tbody>
        <?php
        foreach ($da as $d) {
            print ('<tr><td>'
                . $today->format('Y-m-d (w D)')
                . '</td><td>'
                . $d->format('Y-m-d (w D)')
                . '</td><td>'
                . $today->diff($d)->format('%R%a') // %R=='-'|'+'; %r=='-'|''
                . '</td><td>'
                . $today->diffWorkingWeekdays($d)
                . '</td><td>'
                . $today->diffBusinessDays($d)
                . '</td></tr>'
                . "\n"
            );
        } ?>
    </tbody>
</table>
<h2>Test Reflexivity</h2>
<?php
 $date1 = new DateBiz('2016-09-01');
 $date2 = new DateBiz('2016-09-10');
?>
<table class="table table-condensed table-striped table-responsive">
    <thead>
    <th>Date 1</th>
    <th>Date 2</th>
    <th>Calendar days</th>
    <th>Working weekdays</th>
    <th>Adjusted business days</th>
    </thead>
    <tbody>
    <?php
    for ($i=0;$i<2;$i++) {
        print ('<tr><td>'
            . $date1->format('Y-m-d (w D)')
            . '</td><td>'
            . $date2->format('Y-m-d (w D)')
            . '</td><td>'
            . $date1->diff($date2)->format('%R%a') // %R=='-'|'+'; %r=='-'|''
            . '</td><td>'
            . $date1->diffWorkingWeekdays($date2)
            . '</td><td>'
            . $date1->diffBusinessDays($date2)
            . '</td></tr>'
            . "\n"
        );
        $d = $date2;
        $date2 = $date1;
        $date1 = $d;
    } ?>
    </tbody>
</table>
<h2>Test add/sub business days.</h2>
<table class="table table-condensed table-striped table-responsive">
    <thead>
    <th>Date</th>
    <th>Add business days</th>
    <th>Resulting date</th>
    <th>Business days away</th>
    <th>Error</th>
    </thead>
    <tbody>
        <?php
        $diffs = [-255, -254, -253, -128, -127, -45, -30, -15, -10, -7, -5, -3, -2, -1, 0, 1, 2, 3, 5, 7, 10, 15, 30, 45, 127, 128, 253, 254, 255];
        foreach ($diffs as $diff) {
            $targetDate = $today->add($diff);
            $realdiff = $today->diffBusinessDays($targetDate);
            print ('<tr><td>'
                . $today->format('Y-m-d (w D)')
                . '</td><td>'
                . $diff
                . '</td><td>'
                . $targetDate->format('Y-m-d (w D)') // %R=='-'|'+'; %r='-'|''
                . '</td><td>'
                . $realdiff
                . '</td><td>'
                . ($diff-$realdiff)
                . '</td></tr>'
                . "\n"
            );
        }
        ?>
    </tbody>
</table>

