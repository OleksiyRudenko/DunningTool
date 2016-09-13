<?php

class DateBiz {
	private $v = false; // DateTime $v
    private static $RxDOWdiff = // array[%7 remainder][dow=DateTime::format('w')=={0=Su..6=Sa}] = week-days in remaining incomplete week
        [
            [	0,	0,	0,	0,	0,	0,	0,	],
            [	0,	0,	1,	1,	1,	1,	1,	],
            [	1,	0,	1,	2,	2,	2,	2,	],
            [	2,	1,	1,	2,	3,	3,	3,	],
            [	3,	2,	2,	2,	3,	4,	4,	],
            [	4,	3,	3,	3,	3,	4,	5,	],
            [	5,	4,	4,	4,	4,	4,	5,	],
        ];
    private static $RxDOWaddsub = [ // how many days to add to duration if add() or sub()
        1 => [
                [	1,	0,	0,	0,	0,	0,	2,	],
                [	1,	1,	1,	1,	1,	3,	2,	],
                [	2,	2,	2,	2,	4,	4,	3,	],
                [	3,	3,	3,	5,	5,	5,	4,	],
                [	4,	4,	6,	6,	6,	6,	5,	],
            ],
        -1 => [
                [	2,	0,	0,	0,	0,	0,	1,	],
                [	2,	3,	1,	1,	1,	1,	1,	],
                [	3,	4,	4,	2,	2,	2,	2,	],
                [	4,	5,	5,	5,	3,	3,	3,	],
                [	5,	6,	6,	6,	6,	4,	4,	],
            ]
        ];


    /* @constructor
     * @param $date : 'YYYY-MM-DD' | DateBiz | DateTime
     * @param $adjust : see set()
     */
    function __construct($date=false,$adjust=0) {
        if ($date===false)
            $date=date('Y-m-d');
        $this->set($date,$adjust);
    }

    /* @name set
     * @param $date : 'YYYY-MM-DD' | DateBiz | DateTime
     * @param $adjust : adjust date to the nearest biz date; 0 - no adjustment; -1 - nearest prior biz day; 1 - nearest following biz date
     */
	public function set($date,$adjust=1) {
	    if (is_obj($date,'DateTime'))
	        $this->v = $date;
        else
            if (is_obj($date,'DateBiz'))
                $this->v=clone $date->getDTo();
            else
                $this->v=date_create($date);
        if ($adjust) {
            // adjust date to nearest biz date
            $this->adjust($adjust);
        }
	}
	
	public function getSQL() { return ($this->v!==null) ? $this->format("Y-m-d") : false; }
	public function getDTo() { return $this->v; }

    /**
     * @name incCalendar
     * @desc add or sub 1 calendar day
     * @param int $v
     */
    public function incCalendar($v=1) {
	    $di = new DateInterval('P1D');
	    $this->v = ($v==1)
            ? $this->v->add($di)
            : $this->v->sub($di);
    }

    /**
     * @name adjust
     * @desc adjust current date to the closest business day
     * @param int $dir : +1 - adjust ahead; -1 - adjust retrospectively
     */
    public function adjust($dir=1) {
	    // updateCache so it contains adjustments +- 2 weeks around this date
        // hopefully, there is no full 2 weeks of non-biz days in any culture :)
        $di = new DateInterval('P14D');
        $dfrom = $this->v->sub($di)->format('Y-m-d');
        $dtill = $this->v->add($di)->format('Y-m-d');
        DateBizCache::updateCache($dfrom,$dtill);
        do {
            $recheck = false;
            $adjval=DateBizCache::isCached($this); // we do not expect false
            $isWeekend = $this->isWeekend();
            // adjust if isWeekend and !=+1 OR !isWeekend and ==-1
            if ($isWeekend && $adjval==1 || !$isWeekend && $adjval==-1) {
                $this->incCalendar($dir);
                $recheck = true;
            }
        } while ($recheck);
    }

    /**
     * @name dow
     * @param string $offsetStyle : w|N
     * @return integer : 0-7 - Sunday is 0|7 depending on $offsetStyle
     */
    public function dow($offsetStyle='N') {
        return $this->format($offsetStyle)*1;
    }

    /**
     * @name isWeekend
     * @return bool : true if Sunday or Saturday
     */
    public function isWeekend() {
        $dow = $this->dow();
        return ($dow==0 || $dow>=6) ? true : false;
    }
    // alias
    public function isSatSun() { return $this->isWeekend(); }

    /**
     * @name isMoFr
     * @return bool : true if in Monday..Friday
     */
    public function isMoFr() {
        return !$this->isWeekend();
    }
    // alias
    public function isMonFri() { return $this->isMoFr(); }

	// transparent from DateTime
	public function format($format) {
	    return $this->v->format($format);
	}
    // transparent from DateTime
    public function diff($datetime,$absolute=false) {
        if (is_obj($datetime,'DateBiz'))
            $datetime = $datetime->getDTo();
        return $this->v->diff($datetime,$absolute);
    }

	/*
	 * @name diffWorkingWeekdays
	 * @desc Working weekdays (i.e. Mon-Fri only accounted) count for interval [this;target]
	 * @param $date : 'YYYY-MM-DD' | DateBiz | DateTime
	 * @return false|Integer : negative if target date comes before this
	 */
	public function diffWorkingWeekdays($targetDate) {
        if ($this->v===false)  return false;
        if (!is_obj($targetDate,'DateBiz'))
            $targetDate=new DateBiz($targetDate);
        if ($targetDate->getDTo()===null) return false;

        // get calendar days diff
        $di = $this->v->diff($targetDate->getDTo()); // date interval
        $diValue = $di->days; // calendar days between dates
        $diSign = ($di->invert) ? -1 : 1; // depends on whether targetDate comes before or after this date
        // convert into business days
        $wwdays = ((int)($diValue/7)) * 5 + self::$RxDOWdiff[$diValue%7][$this->v->format('w')];
        return $wwdays * $diSign;
	}
	// alias
    public function diffWW($date) {
        return $this->diffWorkingWeekdays($date);
    }

    /*
	 * @name diffBusinessDays
	 * @desc Difference between this and argument in working weekdays (i.e. Mon-Fri only accounted)
	 * @param $date : 'YYYY-MM-DD' | DateBiz | DateTime
	 * @return false if $this->v === false || argument === false
	 */
    public function diffBusinessDays($date)
    {
        if ($this->v === false) return false;
        if (!is_obj($date, 'DateBiz'))
            $date = new DateBiz($date);
        if ($date->getDTo() === null) return false;

        $wwdays = $this->diffWorkingWeekdays($date);
        $wwsign = ($wwdays<0) ? -1 : 1;
        // use $this->dbt to adjust
        $bizdays = ($wwdays*$wwsign)+$this->getAdjustmentBalance($this->getSQL(),$date->getSQL());

        return $bizdays * $wwsign;
    }
    // aliases
    public function diffBizDays($date) {
        return diffBusinessDays($date);
    }
    public function diffBankDays($date) {
        return diffBusinessDays($date);
    }
    public function diffBankingDays($date) {
        return diffBusinessDays($date);
    }
    public function diffBD($date) {
        return diffBusinessDays($date);
    }

    /**
     * @name add
     * @desc adds $bizdays business days
     * @param $bizdays : integer
     * @return DateBiz : date, at least $bizdays business days after or before $this date
     */
    public function add($bizdays) {
        if ($bizdays==0)
            return new DateBiz($this);
        $sign = ($bizdays<1) ? -1 : 1;
        $bizdays = abs($bizdays);

        // convert business days into calendar
        $weeks = (integer)($bizdays/5);
        $remd = $bizdays%5;
        $calendays = $weeks*7 + self::$RxDOWaddsub[$sign][$remd][$this->dow()];
        // make target date until diff == $bizdays
        $di = new DateInterval('P'.$calendays.'D');
        $retdate = new DateBiz($this,0); // no adjustment on cloning
        if ($sign==1)
            $retdate->v->add($di);
        else
            $retdate->v->sub($di);

        // debug?
        /*
        $debug = false;
        if ($this->diffBusinessDays($retdate) !== $bizdays*$sign) $debug = true;
        if ($debug)
            print
            alert(
                'First approach: '
                . $this->format('Y-m-d (w D)') . ' '
                . ($sign>0?'+':'-')
                . $bizdays . '/'
                . $calendays
                . ' bizdays/calendays = '
                . $retdate->format('Y-m-d (w D)')
                . ' == '
                . $this->diffBusinessDays($retdate)
                . ' biz days away'
            , 'info')
        ; */

        // make and adjust target date until diff == $bizdays
        $count = 0; // limit iterations
        while ($count<10 &&
                (
                    ($diff=$this->diffBusinessDays($retdate)) !== $bizdays*$sign
                )
            ) {
            /* if ($debug)
                print
                    alert(
                        $diff . ' ? '
                        . ($bizdays*$sign)
                        , 'info')
                ; */
            $adjcalendays = $diff-($bizdays*$sign);
            $di = new DateInterval('P'.abs($adjcalendays).'D');
            if ($adjcalendays<0)
                $retdate->v->add($di);
            else
                $retdate->v->sub($di);
            /* if ($debug)
                print
                    alert(
                        '--->: '
                        . $this->format('Y-m-d (w D)') . ': '
                        . $adjcalendays
                        . ' adj calendays => '
                        . $retdate->format('Y-m-d (w D)')
                        . ' == '
                        . $this->diffBusinessDays($retdate)
                        . ' biz days away from original'
                        , 'info')
                ; */
            $count++;
        }
        /* if ($debug)
            print
                alert(
                    '=== '
                    . $this->format('Y-m-d (w D)') . ' '
                    . ($sign>0?'+':'-')
                    . $bizdays . '/'
                    . $calendays
                    . ' bizdays/calendays = '
                    . $retdate->format('Y-m-d (w D)')
                    . ' == '
                    . $this->diffBusinessDays($retdate)
                    . ' biz days away'
                    , 'info')
            ; */
        return $retdate;
    }

    /*
     * @name getAdjustmentBalance
     * @desc sums up required biz days adjustment
     * @param $from : 'YYYY-MM-DD' | DateBiz
     * @param $till : 'YYYY-MM-DD' | DateBiz
     */
	private function getAdjustmentBalance($from,$till) {
	    return DateBizCache::getAdjustmentBalance($from,$till);
    }

}

class DateBizCache
{
    private static $dbh=null;        // DB handler
    private static $dbt=null;        // DB table name
    private static $cache = [];      // cached db requests
    private static $cacheRange = []; // cached range
    private static $dberr = false;   // error
    private static $dberrmsg = '';   // error message

    public static $dowName = ['Вс', 'Пн','Вт','Ср','Чт','Пт','Сб','Вс',];

    /* @name setDB
     * @desc intialize db access on class-level
     * @param $dbh : imysql handler
     * @param $dbt : table name; table structure: { DATETIME Date, SMALLINT Status } Status : -1 = workday declared day-off; 1 = weekend day declared workday
     */
    public static function initialize(&$dbh,$dbt='dateadjustment') {
        self::$dbh = &$dbh;
        self::$dbt = $dbt;
    }

    public static function debug() {
        return
            'tbName='.self::$dbt.'; cacheRange=['
            .(
                count(self::$cacheRange) ? self::$cacheRange[0].','.self::$cacheRange[1]
                    : 'null'
                )
            .']; dberror={'.self::$dberr.','.self::$dberrmsg.'}';
    }

    /**
     * @name getTableName
     * @desc returns cache DB table name
     * @return String
     */
    public static function getTableName() {
        return self::$dbt;
    }

    /**
     * @name error
     * @desc return latest [errorCode, errorMessage]
     * @return array
     */
    public static function error() {
        return [ self::$dberr, self::$dberrmsg ];
    }

    /**
     * @name isCached
     * @param $date : 'YYYY-MM-DD' | DateBiz
     * @return boolean : cache[$date] or 0 if $date within $cacheRange OR false
     */
    public static function isCached($date) {
        if (is_obj($date,'DateBiz')) $date=$date->getSQL();
        if (!count(self::$cacheRange)) return false;
        if ($date>=self::$cacheRange[0] && $date<=self::$cacheRange[1])
            return isset(self::$cache[$date])
                ? self::$cache[$date]
                : 0;
        return false;
    }

    /*
     * @name getAdjustmentBalance
     * @desc sums up required biz days adjustment
     * @param $from : 'YYYY-MM-DD' | DateBiz
     * @param $till : 'YYYY-MM-DD' | DateBiz
     * @return Integer | false
     */
    public static function getAdjustmentBalance($from,$till) {
        return (($a = self::getAdjustmentList($from,$till)) ? array_sum($a) : false);
    }

    /*
     * @name getAdjustmentList
     * @desc get bizdays adjustments from cache/DB
     * @param $from : 'YYYY-MM-DD' | DateBiz
     * @param $till : 'YYYY-MM-DD' | DateBiz
     * @return Array | false
     */
    public static function getAdjustmentList($from, $till) {
        if (!self::updateCache($from,$till)) return false;
        if (is_obj($from,'DateBiz')) $from=$from->getSQL();
        if (is_obj($till,'DateBiz')) $till=$till->getSQL();
        if ($from>$till) list($from,$till) = array($till,$from);
        // get & return adjustments list
        $list=[];
        foreach (self::$cache as $d=>$v) {
            if ($d>=$from) {
                if ($d<=$till)
                    $list[$d]=$v;
                else
                    break; // stop further search
            }
        }
        return $list;
    }

    /**
     * @name  updateCache
     * @desc  updates ::$cache so it contains date adjustments within given range
     * @param $from
     * @param $till
     * @return bool
     */
    public static function updateCache($from, $till) {
        if (self::$dbh===null) return false;
        if (is_obj($from,'DateBiz')) $from=$from->getSQL();
        if (is_obj($till,'DateBiz')) $till=$till->getSQL();
        if ($from>$till) list($from,$till) = array($till,$from);
        $dbInterval = array($from, $till);
        $doQuery = true;
        if (count(self::$cacheRange)) {
            // cache exists; check if db query required
            /* Let us have cached range:  5-9
               Requested range => DB inquiries for ranges
                1- 3  => 1-5
                1- 6  => 1-5
                1-15 => 1-5, 9-15
                7- 8  => nil
                7-15 => 9-12
               13-15 => 9-12
            */

            if ($from>=self::$cacheRange[0] && $till<=self::$cacheRange[1]) {
                $dbQuery = false;
            } else {
                // adjust $db* for DB request
                if ($from<self::$cacheRange[0] && $till>self::$cacheRange[1]) {
                    // requested range contains cached entirely
                    $dbInterval[0] = [ $from, self::$cacheRange[0] ];
                    $dbInterval[1] = [ self::$cacheRange[1], $till ];
                } else {
                    if ($from<self::$cacheRange[0]) {
                        $dbInterval = [ $from, self::$cacheRange[0] ];
                    } else {
                        $dbInterval = [ self::$cacheRange[1], $till ];
                    }
                }
            }
        }
        if ($doQuery) {
            // make request
            if (is_array($dbInterval[0])) {
                $resa=self::dbRequest($dbInterval[0][0],$dbInterval[0][1]);
                if ($resa[0]===false) return false;
                $resa=self::dbRequest($dbInterval[1][0],$dbInterval[1][1]);
                if ($resa[0]===false) return false;
            } else {
                $resa=self::dbRequest($dbInterval[0],$dbInterval[1]);
                if ($resa[0]===false) return false;
            }
        }
        return true;
    }

    /**
     * @name setAdjustment
     * @desc Inserts/Updates $dbt with date and relevant adjustment
     * @param $date : 'YYYY-MM-DD' | [ $date=>value,... ]
     * @param $value : Integer : { 1 = business Sa|Su; -1 = day-off Mo..Fr }
     * @return Boolean
     */
    public static function setAdjustment($dateSQL, $value=0) {
        // logMessage('DateBiz','setAdjustment: '.var_export($dateSQL,true));
        // logMessage('DateBiz','setAdjustment: dbh '.var_export(self::$dbh,true));
        if (self::$dbh===null) return false;
        if (!is_array($dateSQL))
            $dateSQL = [$dateSQL=>$value];
        $poolInsert = []; // [$date]=value
        $poolUpdate = []; // [$date]=value
        $poolDelete = []; // [$date]=0
        $changes = false;
        foreach ($dateSQL as $d=>$v)
        {
            switch ($v) {
                case  1: break;
                case -1: break;
                case  0: break;
                default: $v=0;
            }
            // check if already exists
            if ($result = self::$dbh->query(
                'SELECT * FROM `'
                .self::$dbt
                ."` WHERE AdjDate='".$d."'"
            )) {
                // logMessage('DateBiz','mysqli.result='.var_export($result,true),'info');
                if ($result->num_rows) { // exists
                    if ($v==0)
                        $poolDelete[$d] = 0;
                    else
                        $poolUpdate[$d] = $v;
                } else {
                    $poolInsert[$d] = $v;
                }
            }
        }
        // serve pools: INSERT, UPDATE, DELETE
        if (count($poolInsert)) {
            $values = [];
            foreach ($poolInsert as $d=>$v)
                $values[]="('$d',$v)";
            $q = 'INSERT INTO '.self::$dbt.' (AdjDate, AdjValue) VALUES '
                .implode(',',$values);
            $result=self::$dbh->query($q);
            logMessage('DateBiz',$q.' -- '.($result?'OK':'Fail'),$result?'success':'danger');
            $changes = true;
        }

        if (count($poolUpdate)) {
            $reverse = [];      // 0 => dates..., 1=>dates.., -1=>dates...
            foreach ($poolUpdate as $d=>$v)
                $reverse[$v][]=$d;
            foreach ($reverse as $v=>$da) {
                $values=[];
                foreach ($da as $d)
                    $values[]="AdjDate='$d'";
                $q='UPDATE '.self::$dbt.' SET AdjValue='.$v.' WHERE '
                    .implode(' OR ',$values);
                $result=self::$dbh->query($q);
                logMessage('DateBiz',$q.' -- '.($result?'OK':'Fail'),$result?'success':'danger');
            }
            $changes = true;
        }

        if (count($poolDelete)) {
            $values = [];
            foreach ($poolDelete as $d=>$v)
                $values[]="AdjDate='$d'";
            $q = 'DELETE FROM '.self::$dbt.' WHERE '
                .implode(' OR ',$values);
            $result=self::$dbh->query($q);
            logMessage('DateBiz',$q.' -- '.($result?'OK':'Fail'),$result?'success':'danger');
            $changes = true;
        }

        // update/reset $cache? -- RESET cache!
        if ($changes) {
            self::$cache = [];
            self::$cacheRange = [];
            logMessage('DateBiz','Cache has been reset','info');
        }
    }


    /**
     * @name dbRequest
     * @desc populates self::cache
     * @param $from : 'YYYY-MM-DD' | DateBiz
     * @param $till : 'YYYY-MM-DD' | DateBiz
     * @return [ dbOk ,cacheUpdated == true if cache got updated | false otherwise]
     */
    private static function dbRequest($from, $till) {
        if (self::$dbh===null) return [false,false];
        if (is_obj($from,'DateBiz')) $from=$from->getSQL();
        if (is_obj($till,'DateBiz')) $till=$till->getSQL();
        if ($from>$till) list($from,$till) = array($till,$from);
        if ($result = self::$dbh->query(
            'SELECT * FROM `'
            .self::$dbt
            ."` WHERE AdjDate>='".$from."' AND AdjDate<='".$till."'"
            )) {
            // logMessage('DateBiz','dbRequest: '.var_export($result,true));
            $isCacheUpdated = false;
            if ($result->num_rows) {
                $isCacheUpdated = true;
                while ($row = $result->fetch_assoc()) {
                    // logMessage('DateBiz','dbRequest: '.var_export($row,true));
                    self::$cache[$row['AdjDate']]=$row['AdjValue'];
                    if (count(self::$cacheRange)==0)
                        self::$cacheRange[0] = self::$cacheRange[1] = $row['AdjDate'];
                    else {
                        if (self::$cacheRange[0]>$row['AdjDate'])
                            self::$cacheRange[0]=$row['AdjDate'];
                        if (self::$cacheRange[1]<$row['AdjDate'])
                            self::$cacheRange[1]=$row['AdjDate'];
                    }
                }
            }
            // $result->close();
            return [true,$isCacheUpdated];
        } else {
            self::$dberrmsg = self::$dbh->error;
            return [false,false];
        }


        // parse results. fill $cache up, update $cacheRange
        //!...

        // sort cache by keys if updated
        //!...



    }

    public static function getDowName($date, $offsetStyle='w') {
        $d = new DateTime($date);
        $dow = $d->format($offsetStyle)*1;

        return self::$dowName[$dow];
    }


}