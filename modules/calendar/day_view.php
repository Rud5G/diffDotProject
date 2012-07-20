<?php /* CALENDAR $Id: day_view.php,v 1.34.4.3 2006/01/28 16:32:54 pedroix Exp $ */
global $tab, $locale_char_set, $date;
$AppUI->savePlace();

require_once( $AppUI->getModuleClass( 'tasks' ) );

// retrieve any state parameters
if (isset( $_REQUEST['company_id'] )) {
        $AppUI->setState( 'CalIdxCompany', intval( $_REQUEST['company_id'] ) );
}
$company_id = $AppUI->getState( 'CalIdxCompany', $AppUI->user_company);

$event_filter = $AppUI->checkPrefState('CalIdxFilter', @$_REQUEST['event_filter'], 'EVENTFILTER', 'my');

$AppUI->setState( 'CalDayViewTab', dPgetParam($_GET, 'tab', $tab) );
$tab = $AppUI->getState( 'CalDayViewTab' ,'0');

// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');

// get the passed timestamp (today if none)
$ctoday = new CDate();
$today = $ctoday->format(FMT_TIMESTAMP_DATE);
$date = dPgetParam( $_GET, 'date', $today);
// establish the focus 'date'
$this_day = new CDate( $date );
$dd = $this_day->getDay();
$mm = $this_day->getMonth();
$yy = $this_day->getYear();

// get current week
$this_week = Date_calc::beginOfWeek ($dd, $mm, $yy, FMT_TIMESTAMP_DATE, LOCALE_FIRST_DAY );

// prepare time period for 'events'
$first_time = $this_day;
$first_time->setTime( 0, 0, 0 );
$first_time->subtractSeconds( 1 );

$last_time = $this_day;
$last_time->setTime( 23, 59, 59 );

$prev_day = new CDate( Date_calc::prevDay( $dd, $mm, $yy, FMT_TIMESTAMP_DATE ) );
$next_day = new CDate( Date_calc::nextDay( $dd, $mm, $yy, FMT_TIMESTAMP_DATE ) );

// setup the title block
$titleBlock = new CTitleBlock( 'Day View', 'myevo-appointments.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=calendar&date=".$this_day->format( FMT_TIMESTAMP_DATE ), "month view" );
$titleBlock->addCrumb( "?m=calendar&a=week_view&date=".$this_week, "week view" );
$titleBlock->addCell(
        '<input type="submit" class="button" value="'.$AppUI->_('new event').'">', '',
        '<form action="?m=calendar&a=addedit&date=' . $this_day->format( FMT_TIMESTAMP_DATE )  . '" method="post">', '</form>'
);
$titleBlock->show();
?>
<script language="javascript">
function clickDay( idate, fdate ) {
        window.location = './index.php?m=calendar&a=day_view&date='+idate;
}
</script>

<table width="100%" cellspacing="0" cellpadding="4">
<tr>
        <td valign="top">
                <table border="0" cellspacing="1" cellpadding="2" width="100%" class="motitle">
                <tr>
                        <td>
                                <a href="<?php echo '?m=calendar&a=day_view&date='.$prev_day->format( FMT_TIMESTAMP_DATE ); ?>"><img src="images/prev.gif" width="16" height="16" alt="pre" border="0"></a>
                        </td>
                        <th width="100%">
                                <?php echo htmlentities($this_day->format( "%A" ), ENT_COMPAT, $locale_char_set).', '.$this_day->format( $df ); ?>
                        </th>
                        <td>
                                <a href="<?php echo '?m=calendar&a=day_view&date='.$next_day->format( FMT_TIMESTAMP_DATE ); ?>"><img src="images/next.gif" width="16" height="16" alt="next" border="0"></a>
                        </td>
                </tr>
                </table>

<?php
// tabbed information boxes
$tabBox = new CTabBox( "?m=calendar&a=day_view&date=" . $this_day->format( FMT_TIMESTAMP_DATE ),
        "{$dPconfig['root_dir']}/modules/calendar/", $tab );
$tabBox->add( 'vw_day_events', 'Events' );
$tabBox->add( 'vw_day_tasks', 'Tasks' );
$tabBox->show();
?>
        </td>
<?php if ($dPconfig['cal_day_view_show_minical']) { ?>
        <td valign="top" width="175">
<?php
$minical = new CMonthCalendar( $this_day );
$minical->setStyles( 'minititle', 'minical' );
$minical->showArrows = false;
$minical->showWeek = false;
$minical->clickMonth = true;
$minical->setLinkFunctions( 'clickDay' );

$minical->setDate( $minical->prev_month );

echo '<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr>';
echo '<td align="center" >'.$minical->show().'</td>';
echo '</tr></table><hr noshade size="1">';

$minical->setDate( $minical->next_month );

echo '<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr>';
echo '<td align="center" >'.$minical->show().'</td>';
echo '</tr></table><hr noshade size="1">';

$minical->setDate( $minical->next_month );

echo '<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr>';
echo '<td align="center" >'.$minical->show().'</td>';
echo '</tr></table>';
?>
        </td>
 <?php } ?>
</tr>
</table>
