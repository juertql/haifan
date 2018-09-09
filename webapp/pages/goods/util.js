
function addZero(num) {
  return num < 10 ? '0' + num : num
}
/** 获取某个年-月-日下标 */
function getDayMindex(day,month){
   let length = month.length
   var index = 0;
   for(let i = 0 ; i <= length ; i++){
       if( day == month[i]['value']){
          index = i ;
          break;
       }
   }
   return index;
}
/** 当前年月 && 年月日 */
function getCurrentDate(date) {
  var today = new Date()
  var year  = today.getFullYear()
  var month = today.getMonth() + 1
  var day   = today.getDate()
  var week  = today.getDay()
  //年-月-日
  function getFullDate() {
      return year + String(addZero(month)) + String(addZero(day)) 
  }
  //年-月
  function getYearMonth() {
    return year + '-' + addZero(month)
  }

  return {
    getFullDate,
    getYearMonth,
  }
}

//n年 m月
function translateFormateDate(date) {
  var year  = date.split('-')[0]
  var month = date.split('-')[1]
  return year + '年' + month + '月'
}


//输入年份，判断是润年还是平年
function is_leap(year) {
  return year % 400 === 0  || year % 4 === 0 && year % 100 !== 0 ? true : false
}

//输入年份，获取这一年每个月的天数
function m_days(year) {
  return [31,28+is_leap(year),31,30,31,30,31,31,30,31,30,31]
}

//输入年月，返回这个月的第一天是星期几
//2017-04
function firstDay(date) {
  return new Date(date + '-01').getDay()
}

//输入年月，返回一个数组, 2017-03
function generateDays(date) {
  var year  = date.split('-')[0]
  var month = date.split('-')[1] - 1
  var arr = []
  for(let j = 0; j < firstDay(date); j++) {
    arr.push({value: '', num: ''})
  }
  for(let i = 0; i < m_days(year)[month]; i++) {
     let value = year +  addZero(month+1) + addZero(i+1)

     arr.push({
         num: i+1,
         value: value,
    })
  }
  return arr
}

//接受年月，返回下一个年月
function nextMonth(date) {
  var year  = date.split('-')[0]
  var month = date.split('-')[1]
  if(parseInt(month) <= 11)
    return year + '-' + addZero(parseInt(month) + 1)
  else 
    return parseInt(year) + 1 + '-' + '01'
}

//接受年月，返回前一个年月
function lastMonth(date) {
  var year  = date.split('-')[0]
  var month = date.split('-')[1]

  if(month === '01')
    return parseInt(year) - 1 + '-' + '12'
  else 
    return year + '-' + addZero(parseInt(month) - 1)
}

module.exports = {
    getCurrentDate,
    generateDays,
    translateFormateDate,
    nextMonth,
    lastMonth,
    getDayMindex
}