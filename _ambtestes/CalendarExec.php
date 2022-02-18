<?php
date_default_timezone_set('America/Denver');

//setup more day var
function GetMonthName($n)
{
  $timestamp = mktime(0, 0, 0, $n, 1, 2005);
  return date("F", $timestamp);
}

function title($month, $year) 
{
  $namemonth = GetMonthName($month);
  echo "<h2 align='center'>$namemonth $year</h2>";
}

function startcontrols($smonth, $syear)
{
  //Start Calendar Controls
  $y = 0;
    
  echo "<div align='center'><form method='post'>";
          if($smonth != 1) { $month = $smonth-1; } else { $month = 12;}
          $month = str_pad($month, 2, "0", STR_PAD_LEFT);
          
          if($smonth != 1) { $year = $syear; } else{ $year = $syear-1; }
  echo '<a href="?m='.$month.'&y='.$year.'" class="control"><<     Previous Month</a>';
   
  echo "<select name='m'>";
  for($x = 1; $x <= 12; $x++)
         {
            if(strlen($x) == '1') {$x = $y.$x; }
             echo '<option value="'.$x.'"'.($x != $month ? '' : ' selected="selected"').'>'.date('F',mktime(0,0,0,$x,1,$year)).'</option>';
         }
   echo "</select><select name='y'>";
   for($x = 2010; $x <= 2012; $x++)
         {
              echo '<option value="'.$x.'"'.($x != $year ? '' : 'selected="selected"').'>'.$x.'</option>';
         }
          echo "</select>
          <input type='submit' value='GO'>";
          
          if($smonth != 12) { $month = $smonth+1; } else { $month = 1;}
          $month = str_pad($month, 2, "0", STR_PAD_LEFT);
          
          if($smonth != 12) { $year = $syear; } else{ $year = $syear+1; }
          
          echo '<a href="?m='.$month.'&y='.$year.'" class="control">Next Month >></a>';
          echo "</form>";
          echo "</div>";
}

/* draws a calendar */
function draw_calendar($month,$year){ 
    /* draw table */
    $calendar = '<table cellpadding="0" cellspacing="0" class="calendar" align="center">';

    /* table headings */
    $headings = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
    $calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

    /* days and weeks vars now ... */
    $running_day = date('w',mktime(0,0,0,$month,1,$year)-1);
    
    $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
    $days_in_this_week = 1;
    $day_counter = 0;
    $dates_array = array();
    $today = date('j');
    $nowmonth = date('n');
    $nowyear = date('Y');
  
    /* row for week one */
    $calendar.= '<tr class="calendar-row">';

    /* print "blank" days until the first of the current week */
    for($x = 0; $x < $running_day; $x++):
        $calendar.= '<td class="calendar-day-np">&nbsp;</td>';
        $days_in_this_week++;
    endfor;
   $x = 01;
    /* keep going with days.... */
    for($list_day = '1'; $list_day <= $days_in_month; $list_day++): 
    if($list_day == $today && $month == $nowmonth && $year == $nowyear) {
      $calendar.= '<td class="calendar-day-today" onclick=window.location.href="viewday.php?d='.$list_day.'&m='.$month.'&y='.$year.'">';
      } else {
        //$calendar.= '<td class="calendar-day" onclick=window.location.href="viewday.php?d='.$list_day.'&m='.$month.'&y='.$year.'">';
        $calendar.= '<td class="calendar-day">';
        }
            /* add in the day number */
        $calendar.= '<div class="day-number">'.$list_day.'</div>';    

       $y = 0;
       if(strlen($x) == '1') {$x = $y.$x; }
       
    
            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
            $todaydate = $year.'-'.$month.'-'.$x;
    $query = "SELECT * FROM `events` WHERE '$todaydate' >= event_start AND '$todaydate' <= event_end";
  $results = mysql_query($query) or die (mysql_error());
            if (mysql_num_rows($results) > '0') {
              while($row = mysql_fetch_array($results)){ 
              extract($row);      
       $catquery = "SELECT * FROM categories WHERE id=$catid";
       $catres = mysql_query($catquery) or die (mysql_error());
       $catrow = mysql_fetch_array($catres);
       $cat = $catrow['color'];
     $calendar.= '<div style="margin-top:2px; height:10px; width:10px; float:left; background:'.$cat.'">&nbsp;</div><div class="calendar-text"><a href="viewevent.php?id='.$id.'" rel="shadowbox;width=400;height=330">'.$event.'</a></div>';
         
      /* $catquery = "SELECT * FROM categories WHERE id=$catid";
       $catres = mysql_query($catquery) or die (mysql_error());
       $catrow = mysql_fetch_array($catres);
       $cat = $catrow['name'];
    $calendar.= '<div class="'.$cat.'">&nbsp;</div><div class="calendar-text"><a href="viewevent?id='.$id.'">'.$event.'</a></div>';
     */
     //end while  
     }
     //end num_row if  
     } else {  $calendar.= '<div>&nbsp;</div>';} 
     $x++;     
        $calendar.= str_repeat('<p>&nbsp;</p>',2);    
        $calendar.= '</td>';
        if($running_day == 6):
            $calendar.= '</tr>';
            if(($day_counter+1) != $days_in_month):
                $calendar.= '<tr class="calendar-row">';
            endif;
            $running_day = -1;
            $days_in_this_week = 0;
        endif;
        $days_in_this_week++; $running_day++; $day_counter++;
      endfor;
    /* finish the rest of the days in the week */
    if($days_in_this_week < 8):
        for($x = 1; $x <= (8 - $days_in_this_week); $x++):
            $calendar.= '<td class="calendar-day-np">&nbsp;</td>';
        endfor;
    endif;

    /* final row */
    $calendar.= '</tr>';

    /* end the table */
    $calendar.= '</table>';
    
    /* all done, return result */
    return $calendar;
}
?>