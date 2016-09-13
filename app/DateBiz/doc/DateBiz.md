# DateBiz

Provides a set of methods to operate dates in terms of business days.

NB! All dates here presented in ISO YYYY-MM-DD format.

## Methods

### diffWorkingWeekdays($targetDate)

Calculates number of working weekdays (i.e. Mon-Fri only accounted) 
for interval between this date and target date:
 - negative for interval [target;this]
 - positive for interval [this;target]
  
This date **is accounted**.

Examples:

 * 2016-09-05 (Mo) diff 2016-09-11 (Su) = 5
 * 2016-09-06 (Tu) diff 2016-09-03 (Sa) = -2
 
#### Algorithm

```
  days = number of calendar days between dates 
  fullWeeks =  rounddown(days / 7) // Calculate number of full weeks in interval
  remainder = days % 7 // duration of "incomplete" week
```
The result equals `fullWeeks * 5 + (number of weekdays in the remainder)`

The `number of weekdays in the remainder` depends on the day of week such 
"incomplete" week commences on.

Look at the following chart for the interval 2016-09-01 (Th) - 2016-09-10 (Sa).

**`Th1`**  **`Fr1`** `Sa1` `Su1` **`Mo1`** **`Tu1`** **`We1`** **`Th2`** **`Fr2`** `Sa2`
 
So, we have 10 days in total, 1 full week and the remainder over full weeks 
equals 3 days. 
But is remainder a head or a tail of the interval? Does it matter? Let's see.
 
```
remainder + full weeks == [Th1;Sa1] + [Su1;Sa2] == 2 + 5 weekdays
full weeks + remainder == [Th1;We1] + [Th2;Sa2] == 5 + 2 weekdays
```


 





