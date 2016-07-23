<?php
/**
 * Created by PhpStorm.
 * User: Rudenko
 * Date: 18/07/2016
 * Time: 12:11
 */

// ui form component (tabbed): get data from db; add/remove/edit db entries; tests

        if (!isset($_COOKIE['DateBizListFrom']))
            $_COOKIE['DateBizListFrom'] = date('Y-01-01');
        if (!isset($_COOKIE['DateBizListTill']))
            $_COOKIE['DateBizListTill'] = date('Y-12-31');
        // get data from DB
        // echo alert(var_export($_COOKIE,true));
        $dlist = DateBizCache::getAdjustmentList($_COOKIE['DateBizListFrom'],$_COOKIE['DateBizListTill']);
        // echo div($_COOKIE['DateBizListFrom'].' .. '.$_COOKIE['DateBizListTill']);
        // echo p('Query result: '.var_export($dlist,true));
        // echo div(alert(DateBizCache::debug(),'info'));

        echo unlogMessage('DateBiz');
        ?>
        <!-- h2>Dynamic Tabs</h2 -->
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#dateBizList">Переносы</a></li>
            <li><a data-toggle="tab" href="#dateBizTest">Тесты</a></li>
        </ul>

        <div class="tab-content">
            <div id="dateBizList" class="tab-pane fade in active">
                <h3>Переносы</h3>
                <form id="formDateBizManage" role="form" class="form-horizontal" method="POST">
                    <div class="form-group">
                        <label for="formDateBizManage-from" class="control-label col-sm-offset-2 col-sm-1">
                            с:</label>
                        <div class="col-sm-4">
                            <input id="formDateBizManage-from" name="from" type="date" class="form-control datepicker"
                                   value="<?=$_COOKIE['DateBizListFrom']?>">
                        </div>
                        <div class="col-sm-4">&nbsp;
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="formDateBizManage-till" class="control-label col-sm-offset-2 col-sm-1">
                            по:</label>
                        <div class="col-sm-4">
                            <input id="formDateBizManage-till" name="till" type="date" class="form-control datepicker"
                                   value="<?=$_COOKIE['DateBizListTill']?>">
                        </div>
                        <div class="col-sm-4">&nbsp;
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-10">
                            <button name="submitDateBiz" type="submit" class="btn btn-default" value="SelectPeriod">Показать</button>
                        </div>
                    </div>
                </form>
                <hr/>
                <div class="col-sm-offset-3 col-sm-9">
                    <strong class="lead"><?=div($_COOKIE['DateBizListFrom'].' .. '.$_COOKIE['DateBizListTill'])?></strong>
                </div>
                <form id="formDateBizManage" role="form" class="form-horizontal" method="POST">
                    <div class="col-sm-offset-3 col-sm-4">
                        Дата
                        <div class="small text-muted">
                            ГГГГ-ММ-ДД
                        </div>
                    </div>
                    <div class="col-sm-4">
                        Значение
                        <div class="small text-muted">
                            -1 - Пн..Пт =&gt; выходной <br/>
                            1 - Сб..Вс =&gt; рабочий
                        </div>
                    </div>
                    <?php
                    // generate update fields
                    if ($dlist!==false && count($dlist)) {
                        $i=0;
                        foreach ($dlist as $d=>$v) {
                            ?>
                            <div class="form-group">
                                <label id="oldDateDow<?=$i?>" for="oldDate<?=$i?>" class="control-label col-sm-offset-2 col-sm-1">
                                    <?=DateBizCache::getDowName($d)?>
                                </label>
                                <div class="col-sm-4">
                                    <input id="oldTouched<?=$i?>" name="oldTouched[<?=$i?>]" type="hidden" value="0">
                                    <input id="oldDate<?=$i?>" name="oldDate[<?=$i?>]" type="date" class="form-control" value="<?=$d?>"
                                           READONLY
                                           >
                                </div>
                                <div class="col-sm-2">
                                    <div class="checkbox">
                                        <label>
                                            <input name="oldValue[<?=$i?>]" type="checkbox" id="blankCheckbox" value="0" aria-label="delete"
                                                   onchange="document.getElementById('oldTouched<?=$i?>').value=1;">
                                            [<strong class=""><?=$v?></strong>]&nbsp;удалить
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $i++;
                        }
                    } ?>
                    <div class="container">
                        <div class="col-sm-offset-2 col-sm-6">Добавить:</div>
                    </div>
                    <?php
                    // generate new entries fields
                    for ($i=0;$i<10;$i++) {
                        ?>
                    <div class="form-group">
                        <label id="newDateDow<?=$i?>" for="newDate<?=$i?>" class="control-label col-sm-offset-2 col-sm-1">
                            ??</label>
                        <div class="col-sm-4">
                            <input id="newTouched<?=$i?>" name="newTouched[<?=$i?>]" type="hidden" value="0">
                            <input id="newDate<?=$i?>" name="newDate[<?=$i?>]" type="date" class="form-control datepicker xxx--DateBizNewDate"
                                   xxx-data-datebizidx="<?=$i?>"
                                   value=""
                                   onchange="document.getElementById('newTouched<?=$i?>').value=1;
                                       setDOW(this.value,'newDateDow<?=$i?>');
                                       setDOWvalue(this.value,'newValue<?=$i?>');">
                        </div>
                        <div class="col-sm-2">
                            <input id="newValue<?=$i?>" name="newValue[<?=$i?>]" type="text"
                                   class="form-control" value="0" READONLY>
                        </div>
                    </div>
                    <?php
                    }
                ?>
                    <div class="col-sm-offset-2 col-sm-10">
                        <button name="submitDateBiz" type="submit" class="btn btn-default" value="Update">Сохранить</button>
                    </div>
                </form>
            </div>
            <div id="dateBizTest" class="tab-pane">
                <h3>Тесты</h3>
                <?php include('app/DateBiz/DateBiz.view.test.php'); ?>
            </div>
        </div>

