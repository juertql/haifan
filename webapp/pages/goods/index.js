// pages/calendar/calendar.js
var app = getApp()
import { 
    getCurrentDate,
    generateDays,
    translateFormateDate,
    nextMonth,
    lastMonth,
    getDayMindex
} from './util'

Page({
  data:{
    date: '2017-05',
    week: ['日','一','二','三','四','五','六'],
    months: [{}],
    hiddenAddModal:true,//新增模态框
    todo:'',//操作日期
    toDayYMD:'',//今天
    toDayMindex:0,//今天月份下标
    showIndex:0,//显示
    monthOrder:[],//某月订单
    orderDetail:[],//订单详情
    sn:'',
    goods_type:[],
    indicatorDots: true,
    vertical: false,
    autoplay: true,
    interval: 3000,
    duration: 1000,
    currentTab: 0,
    cantSend:'6',
    cantEdit:2

  },

  onLoad:function(options){
    var that = this
    /** 判断是否登录 */
    var userId = wx.getStorageSync('userId')
    if (userId <= 0) {
        this.userLogin();
        return;
    }

    var currentDate = getCurrentDate()//年-月 && 年-月-日
    var year_month = currentDate.getYearMonth()
    var month = generateDays(currentDate.getYearMonth())
    
    console.log("year month: " + year_month + " date: " + currentDate.getFullDate());

    //获取月份订单
    this.getMonthOrder(year_month,month);
    this.getType()
    that.getSet()
    that.getSetA(true)

    //处理数据
    this.setData({
      todo: Number(currentDate.getFullDate()),
      toDayYMD: Number(currentDate.getFullDate()),//今天
      date: currentDate.getYearMonth(),//年-月
      dateCN: translateFormateDate(currentDate.getYearMonth()),//n年m月
      //months: month
    })



    //添加，编辑，取消订单转跳到修改月份
    try
    {
      if (options.year_months_day) {
        var option_ymd = options.year_months_day;
        var option_month = option_ymd.substr(0, 4) + "-" + option_ymd.substr(4, 2);
        console.log("options ymd: " + options.year_months_day + " options month: " + option_month);
        if (currentDate.getYearMonth() != option_month) {
          this.showMonthOrder(option_ymd, option_month)
        }
      }
    }catch(err)
    {
      wx.showToast({
        title: '取消订单失败！',
        duration: 2000
      });
      console.log("exception show Month order." + String(err))
    }

  },
showMonthOrder: function(ymd,ym)
{
  var that = this
  var year_month = ym
  var month = generateDays(ym)
  this.setData({
    showIndex: ''
  })
  //获取月份订单
  this.getMonthOrder(year_month, month);
  //this.getType()
  //that.getSet()
  //that.getSetA(true)
  console.log("month order: " + that.data.todo + " " + that.data.toDayYMD)
  this.setData({
    //todo: Number(currentDate.getFullDate()),
    //toDayYMD: Number(currentDate.getFullDate()),//今天
    months: generateDays(ym),
    date: ym,
    dateCN: translateFormateDate(ym)
  })
},
  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
      var that = this
      /** 判断是否登录 */
      var userId = wx.getStorageSync('userId')
      if (userId <= 0) {
          this.userLogin();
          return;
      }
      var currentDate = getCurrentDate()//年-月 && 年-月-日
      var year_month = currentDate.getYearMonth()
      var month = generateDays(currentDate.getYearMonth())
      //获取月份订单
      this.getMonthOrder(year_month, month);
      this.getType()
      that.getSet()
      that.getSetA(true)
      //处理数据
      this.setData({
          todo: Number(currentDate.getFullDate()),
          toDayYMD: Number(currentDate.getFullDate()),//今天
          date: currentDate.getYearMonth(),//年-月
          dateCN: translateFormateDate(currentDate.getYearMonth()),//n年m月
          //months: month
      })


  }
    ,

  /** 登录 */
  userLogin: function () {
      wx.redirectTo({
          url: '/pages/login/index'
      });
  },
  //set
  getSet:function(){
      var that = this
      wx.request({
          url: app.cg.hostUrl + '/order/set',
          method: 'get',
          data: {
          },
          success: function (res) {
              console.log(res)
              if (res.data.code == 200) {
                  let data = res.data.data
                  that.setData({
                      cantSend: data,
                  })
              }
          }
      })
  },
  //set
  getSetA: function (a) {
      a||false
      var that = this
      wx.request({
          url: app.cg.hostUrl + '/order/setA',
          method: 'get',
          data: {
          },
          success: function (res) {
              console.log(res)
              if (res.data.code == 200) {
                  let data = Number(res.data.data) 
                  console.log("can not edit date: " + data)
                  that.setData({
                      cantEdit: data,
                  })
              }
          },
           complete: function () {
              if (a == true) {
                  wx.stopPullDownRefresh()
              }
          } 
      })
  },
  /** 分类 */
  getType: function () {
      var that = this
      wx.request({
          url: app.cg.hostUrl + '/goods/getGoodsType',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
              is_show: 1
          },
          success: function (res) {
              console.log(res)
              if (res.data.code == 200) {
                  let data = res.data.data
                  that.setData({
                      goods_type: data,

                  })
              }
          }
      })
  },
  /** 处理订单数据 */
  sortMonthOrder: function(order,months){
      var that = this 
      var currentDate = getCurrentDate()
     let months_length = months.length 
     let order_length = order.length
     var nowIndex = 0;
     var flag = false;
     if(order_length > 0){
         for (let i = 0; i < months_length; i++) {
            if(months[i].value != ''){              
                for (let b = 0; b < order_length; b++) {   
                    if (months[i].value == order[b].year_months_day){                      
                        months[i]['sn'] = order[b]['sn']
                        months[i]['order_status'] = order[b]['order_status']
                    }
                    if (Number(currentDate.getFullDate()) == (months[i].value)){
                        flag = true
                        nowIndex = i                       
                        var todo = months[i].value
                        var sn = months[i].sn
                        var order_status = months[i].order_status                                     
                    }
                }
            }         
         }   
     }
     console.log(order)
     that.setData({
         months: months,
         toDayMindex: nowIndex
     })
     if (flag === true) {
         that.showStatus(todo, order_status, sn)
     }    
  },
 /** 获取某月订单 */
 getMonthOrder:function(month,months){
     var that = this
     wx.showLoading({
         title: '加载中',
     })
     var userId = wx.getStorageSync('userId')
     var data = that.data
     wx.request({
         url: app.cg.hostUrl + '/order/getMonthOrder',
         method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
         header: {
             'content-type': 'application/json'
         },
         data: {
             user_id: userId,
             month: month,             
         },
         success: function (res) {
             console.log(res)
             if(res.data.code == 200){
                 let data = res.data.data
                
                 that.setData({
                     monthOrder:data,
                 })              
             }
             that.sortMonthOrder(res.data.data, months)          
         },
         fail: function () {
             // fail
             wx.showToast({
                 title: '网络异常！',
                 duration: 2000
             });
         },
         complete:function(){
             wx.hideLoading()
         }
     })
 },
 /** picker */
  bindPickerChange: function(e) {
      var year_month = e.detail.value
      var month = generateDays(e.detail.value)
      this.setData({
          showIndex: ''
      })
      //获取月份订单
      this.getMonthOrder(year_month, month);
    this.setData({
      months: generateDays(e.detail.value),
      date: e.detail.value,
      dateCN: translateFormateDate(e.detail.value)
    })
  },
/** 上月 */
  bindNextMonth: function() {
      var year_month = nextMonth(this.data.date)
      var month = generateDays(nextMonth(this.data.date))
      this.setData({
          showIndex: ''
      })
      //获取月份订单
      this.getMonthOrder(year_month, month);
    this.setData({
      date: nextMonth(this.data.date),
      dateCN: translateFormateDate(nextMonth(this.data.date)),
      months: generateDays(nextMonth(this.data.date))
    })
  },
/** 下月 */
  bindLastMonth: function() {
      var year_month = lastMonth(this.data.date)
      var month = generateDays(lastMonth(this.data.date))
      this.setData({
          showIndex: ''
      })
      //获取月份订单
      this.getMonthOrder(year_month, month);
      
    this.setData({
      date: lastMonth(this.data.date),
      dateCN: translateFormateDate(lastMonth(this.data.date)),
      months: generateDays(lastMonth(this.data.date))
    })
  },
  /** 增加订单操作 */
  addOrder:function(todo){
          var todo = this.data.todo  
      wx.navigateTo({
          url: '../addOrder/index?year_months_day='+todo,
      })
  },
  /** 编辑订单 */
  editOrder:function(sn,todo){
     var that = this 
     var sn = that.data.sn
     var cantEdit = Number(that.data.cantEdit)
     var todo = Number(that.data.todo)
     var toDayYmd = Number(that.data.toDayYMD)
     /*if (todo <= (toDayYmd + 1)){ 
         wx.showToast({
             title: '不能编辑',
             duration: 1000
         });
         return false;   
     }*/
     var flag = true
     if (todo <= (toDayYmd + cantEdit)) {
         var hours = new Date().getHours();
         console.log(hours)
         if (hours >= 17) {
             wx.showToast({
                 title: '不能编辑',
                 duration: 1000
             });
             return false; 
         }
         flag = false   
     }
     wx.navigateTo({
         url: '../order/edit/index?sn=' + sn + '&year_months_day=' + todo + '&flag=' + flag,
     })
  },
  /** 取消订单 */
  cancelOrder:function(sn,todo){
      var that = this
      var sn = that.data.sn
      var cantEdit = Number(that.data.cantEdit)
      var todo = Number(that.data.todo) 
      var toDayYmd = Number(that.data.toDayYMD)
      var hours = new Date().getHours();
      console.log(todo)
      console.log(toDayYmd)
      
      if(todo > (toDayYmd + cantEdit)){
          that.setData({
              hiddenAddModal:false
          })
      }
      else{
          if (hours >= 17) {
              wx.showToast({
                  title: '不能取消',
                  duration: 1000
              });
              return false;
          }  
          that.setData({
              hiddenAddModal: false
          })     
      }
  },
  /** 确认取消 */
  listenerorderConfirm:function(e){
      var that = this
      var sn = e.target.dataset.value
      var userId = wx.getStorageSync('userId')
      wx.request({
          url: app.cg.hostUrl + '/order/cacelOrder',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
              user_id: userId,
              sn: sn,
          },
          success: function (res) {
              if (res.data.code == 200) {
                  wx.showToast({
                      title: '取消成功',
                      icon: 'success',
                      duration: 2000
                  })  
                console.log("cancel todo: " + that.data.todo)
                wx.reLaunch({
                  url: '/pages/goods/index?year_months_day=' + that.data.todo
                })
              }
              else {
                  
              }
              that.setData({
                  hiddenAddModal: true
              })
             that.onLoad()
          },
          fail: function () {
              // fail
              wx.showToast({
                  title: '网络异常！',
                  duration: 2000
              });
              that.setData({
                  hiddenAddModal: true
              })
          }
      })
     
  },
  /** 取消取消订单 */
  listenerCancel:function(){
    this.setData({
        hiddenAddModal: true 
    })
  },
  /** 订单详情 */
  orderDeatil:function(sn){
      var that = this
      var userId = wx.getStorageSync('userId')
      var data = that.data
      wx.request({
          url: app.cg.hostUrl + '/order/getOrderDetail',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
              user_id: userId,
              sn: sn,
          },
          success: function (res) {
              
              if (res.data.code == 200) {
                that.setData({
                    orderDetail: res.data.data
                })
              }
              else{
                  wx.showToast({
                      title: res.data.msg,
                      duration: 1000
                  });
              }
          },
          fail: function () {
              // fail
              wx.showToast({
                  title: '网络异常！',
                  duration: 2000
              });
          }
      })
  },
  /** 操作日期 */
  click: function(e) {
      var that = this
      var ymd =  e.currentTarget.dataset.item.value
      var todo = Number(e.currentTarget.dataset.item.value) ;
      var order_status = e.currentTarget.dataset.item.order_status
      var sn = e.currentTarget.dataset.item.sn
      that.setData({
          todo: todo
      })
    console.log("todo: " + e.currentTarget.dataset.item.value)
      that.showStatus(todo, order_status, sn)
    //判断
      /**wx.showActionSheet({
          itemList: ['添加订单', '编辑订单', '取消订单'],
          success: function (res) {
              //添加
              if (res.tapIndex === 0 ){
                  that.addOrder(ymd)  
              }
              console.log(res.tapIndex)
          },
          fail: function (res) {
              console.log(res.errMsg)
          }
      })*/
  },
  showStatus: function (todo,order_status,sn) {
      var that = this
      var cantSend = that.data.cantSend
      var cantEdit = Number(that.data.cantEdit)
      var toDayYmd = Number(that.data.toDayYMD) 
      var day = String(todo).substr(0, 4) + '-' + String(todo).substr(4, 2) + '-' + String(todo).substr(6, 4) 
      var d = new Date(day)
      var week = String(d.getDay());
      console.log("week: "+week+" day: ");
      if (String(cantSend).indexOf(week) != '-1'){
          that.setData({
              showIndex: 6
          })  
          return;
      }
      /** 未选餐 不可选 */
      if (order_status == undefined && todo <= (toDayYmd + cantEdit)) {
          that.setData({
              showIndex: 0
          })
          return
      }
      //未选餐 可选 
      if (order_status == undefined && todo > (toDayYmd + cantEdit)) {
          var hours = new Date().getHours();
          console.log(hours)
          if (todo == (toDayYmd + cantEdit)){
              if (hours >= 17) {
                  that.setData({
                      showIndex: 0
                  })
                  return;
              }
              
          }
         
          that.setData({
              showIndex: 1
          })
          return
      }
      //已完成
      if (todo < (toDayYmd + 2) && order_status == 2) {
          that.orderDeatil(sn)
          that.setData({
              showIndex: 2
          })
          return
      }
      //已选餐 可编辑
      if (order_status == 1 && todo >= (toDayYmd+1)) {
          that.orderDeatil(sn)
          that.setData({
              showIndex: 3,
              sn: sn
          })
          return
      }
      //已选餐 不可编辑
      if (order_status == 1 && todo <= (toDayYmd+cantEdit)) {
          that.orderDeatil(sn)
          that.setData({
              showIndex: 4,
              sn: sn
          })
          return
      }
  },

})





