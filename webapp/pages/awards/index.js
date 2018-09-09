var app = getApp()

Page({
  data: {
    awardsList: [],
    animationData: {},
    btnDisabled: '',
    region: ['广东省', '广州市', '海珠区'],
    customItem: '全部'
  },
  
  gotoList: function() {
    wx.navigateTo({
      url: '/pages/user/gift/index'
    })
  },
  bindRegionChange: function (e) {
      console.log('picker发送选择改变，携带值为', e.detail.value)
      this.setData({
          region: e.detail.value
      })
  },
  /** 登录 */
  userLogin: function () {
      wx.redirectTo({
          url: '../login/index'
      });
  },
  onLoad: function(e){
      var that = this
     
     
  },
  /** 获取礼品 */
  getGiftList: function () {
      var that = this
      wx.request({
          url: app.cg.hostUrl + '/gift/getList',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
          },
          success: function (res) {
              if (res.data.code == 200) {

                  that.awardsConfig.awards = res.data.data

              }
          }
      })
  },

  /** 判断是否能抽奖 */
  checkGiftChance: function () {
      var that = this
      var userId = wx.getStorageSync('userId')

      wx.request({
          url: app.cg.hostUrl + '/gift/checkGiftChance',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
              user_id: userId,
          },
          success: function (res) {
              that.awardsConfig.chance = res.data.data;
          }
      })
  },
  /** 判断是否有库存 */
  addGift: function (index, awardsConfig, awardIndex, arr_length){
      var that = this
      var userId = wx.getStorageSync('userId')
    
      wx.request({
          url: app.cg.hostUrl + '/gift/add',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
          user_id:userId,
          gift_id:index
          },
          success: function (res) {
              console.log(res)
              var flag = false
              if (res.data.code == 200) {
                  flag = true;
              } 
              if(!flag){               
                  wx.showModal({
                      title: '抱歉',
                      content: '网络异常，请重试',
                      showCancel: false
                  })
                  return false;     
              }
              else{
                  that.giftTo(awardsConfig, awardIndex, arr_length)
              }                                 
          }
      })    
  },
  //抽奖
  getLottery: function () {
      var that = this
      var userId = wx.getStorageSync('userId')
      //未登录
      if (userId <= 0) {
          wx.showToast({
              title: '请登录！',
              duration: 1000
          });
         this.userLogin()
      } 
      else{
    //分区间
   var list = app.awardsConfig.awards
   var length = list.length;
   var arr = [{}];//区间数组
   var f_val = 0;//初始值
   for(var a = 0; a < length ;a++){
       var n_val = parseFloat(list[a]['chance']) * 100;
       var index = list[a]['id'];//id
       var value = 'value';
       let id = 'id'
       f_val += n_val
       if(f_val > 100){
           f_val = 100  
       }  
       arr[a] = {'value':f_val ,'id':index}
             
   }
   console.log(arr) 
    //计算概率
   var rangeNum = Math.random() * 100 >>> 0;
   console.log(rangeNum)
   var arr_length = arr.length
   var awardIndex = 0;//抽中index
   var awardId = 0;//抽中Id
   for (var b = 0; b < arr_length ; b++ ){
       var l_num = parseFloat(30) 
       if(b != 0){
           l_num = parseFloat(arr[b - 1]['value']) 
       }
      
       if (l_num <= rangeNum && rangeNum <= parseFloat(arr[b]['value']) ){               
               awardIndex = b;       
                  break;    
           }              
   }
   

    // 获取奖品配置
   var awardsConfig = app.awardsConfig
   //console.log(awardsConfig); return false;
    //判断是否有库存
       var id = awardsConfig.awards[awardIndex]['id']
       var types = awardsConfig.awards[awardIndex]['type']
       if(types != 3){
           that.addGift(id, awardsConfig, awardIndex, arr_length) 
       }
       else{
           that.giftTo(awardsConfig, awardIndex, arr_length)
       }
                
   

    /** do */
   
    /*wx.request({
      url: '../../data/getLottery.json',
      data: {},
      header: {
          'Content-Type': 'application/json'
      },
      success: function(data) {
        console.log(data)
      },
      fail: function(error) {
        console.log(error)
        wx.showModal({
          title: '抱歉',
          content: '网络异常，请重试',
          showCancel: false
        })
      }
    })*/
      }
  },
  /** 旋转 */
  giftTo: function (awardsConfig,awardIndex,arr_length){
      var that = this
      if (awardIndex < 2) awardsConfig.chance = false
      console.log(awardIndex)
      // 初始化 rotate
      var animationInit = wx.createAnimation({
          duration: 1
      })
      this.animationInit = animationInit;
      animationInit.rotate(0).step()
      this.setData({
          animationData: animationInit.export(),
          //btnDisabled: 'disabled'
      })

      // 旋转抽奖
      setTimeout(function () {
          var animationRun = wx.createAnimation({
              duration: 4000,
              timingFunction: 'ease'
          })
          that.animationRun = animationRun
          animationRun.rotate(360 * 8 - awardIndex * (360 / arr_length)).step()
          that.setData({
              animationData: animationRun.export()
          })
      }, 100)

      // 记录奖品
      var winAwards = wx.getStorageSync('winAwards') || { data: [] }
      winAwards.data.push(awardsConfig.awards[awardIndex].name + '1个')
      wx.setStorageSync('winAwards', winAwards)
      // 中奖提示
      setTimeout(function () {
          let type = awardsConfig.awards[awardIndex].type
          var title = '恭喜'
          if (type === 3) {
              var title = '遗憾'
          }
          wx.showModal({
              title: title,
              content: '获得' + (awardsConfig.awards[awardIndex].name),
              showCancel: false
          })
          if (awardsConfig.chance) {
              awardsConfig.chance = false
              that.setData({
                  btnDisabled: 'disabled'
              })
          }
      }, 4100);
  },
  cretaeT:function(){
      var that = this;
    
      // 绘制转盘
      var awardsConfig = app.awardsConfig.awards,
          len = awardsConfig.length,
          rotateDeg = 360 / len / 2 + 90,
          html = [],
          turnNum = 1 / len  // 文字旋转 turn 值
      that.setData({
          btnDisabled: app.awardsConfig.chance ? '' : 'disabled'
      })
      var ctx = wx.createContext()
      for (var i = 0; i < len; i++) {
          // 保存当前状态
          ctx.save();
          // 开始一条新路径
          ctx.beginPath();
          // 位移到圆心，下面需要围绕圆心旋转
          ctx.translate(140, 140);
          // 从(0, 0)坐标开始定义一条新的子路径
          ctx.moveTo(0, 0);
          // 旋转弧度,需将角度转换为弧度,使用 degrees * Math.PI/180 公式进行计算。
          ctx.rotate((360 / len * i - rotateDeg) * Math.PI / 180);
          // 绘制圆弧
          ctx.arc(0, 0, 140, 0, 2 * Math.PI / len, false);

          // 颜色间隔
          if (i % 2 == 0) {
              ctx.setFillStyle('#ffb820');
          } else {
              ctx.setFillStyle('#f7d337');
          }

          // 填充扇形
          ctx.fill();
          // 绘制边框
          ctx.setLineWidth(0.5);
          ctx.setStrokeStyle('#e4370e');
          ctx.stroke();

          // 恢复前一个状态
          ctx.restore();

          // 奖项列表
          html.push({ turn: i * turnNum + 'turn', award: awardsConfig[i].name });
      }
      that.setData({
          awardsList: html
      });

      wx.drawCanvas({
          canvasId: 'lotteryCanvas',
          actions: ctx.getActions()
      })
      console.log(that.data.awardsList)
  
  },
  onReady: function (e) {

    var that = this;
    that.cretaeT()
    // wx.setStorageSync('awardsConfig', JSON.stringify(awardsConfig))
    
    console.log(app.awardsConfig)
  }

})
