/**
 * Created by Rudenko on 18/07/2016.
 */
// finds DOW for dateSQLstr and updates targetElIdStr inner HTML relevantly
function setDOW(dateSQLstr,targetElIdStr) {
    // YYYY-MM-DD
    //console.log(dateSQLstr);
    if (dateSQLstr.length<10)
        return;
    //console.log(targetElIdStr);
    var weekday = [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"];
    var d = new Date(dateSQLstr);
    //console.log(d);
    document.getElementById(targetElIdStr).innerText = weekday[d.getDay()];
}

function setDOWvalue(dateSQLstr,targetElIdStr) {
    // YYYY-MM-DD
    //console.log(dateSQLstr);
    if (dateSQLstr.length<10)
        return;
    // console.log(targetElIdStr);
    var d = new Date(dateSQLstr);
    // console.log(d);
    var dow = d.getDay();
    var val = -1; // make non-biz
    if (dow==0 || dow==6) val = 1; // make biz
    document.getElementById(targetElIdStr).value = val;
}