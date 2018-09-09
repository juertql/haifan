//app.js

App({
    //系统数据
   cg: {
       hostUrl:'https://hifan.chuxinbuer.com/haifan/public/api.php/',//接口地址
       //hostUrl: 'http://localhost/hifan1/public/api.php/',//接口地址
       userId:0,
       userRestFoodsNum:0,//可用餐数
       userTotalFoodsNum: 0,//总餐数
       address:'',
       userData:{}//会员信息
   },
   awardsConfig: {
       chance: false,
       awards: [
           {'id': 0, 'name': '谢谢参与','type':0,'chance':'0.2'},
           { 'id': 1, 'name': '1元红包', 'type': 1, 'chance': '0.1'},
           { 'id': 2, 'name': '谢谢参与', 'type': 0, 'chance': '0.05'},
           { 'id': 3, 'name': '5元红包', 'type': 1,'chance': '0.1'},
           { 'id': 4, 'name': '谢谢参与', 'type': 0, 'chance': '0.05'},
           { 'id': 5, 'name': '10元红包', 'type': 1, 'chance': '0.5'}
       ]
   },
  onLaunch: function () {
      var that = this
      that.getUserInfo()
      /** 获取礼品 */
      that.getGiftList()
      /** 获取抽奖参数 */
      that.checkGiftChance()
      console.log('App Launch')
  },
  onShow:function(){
      console.log('1')
  },
  /** 获取礼品 */
  getGiftList: function () {
      var that = this
      wx.request({
          url: that.cg.hostUrl + '/gift/getList',
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
          url: that.cg.hostUrl + '/gift/checkGiftChance',
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
/** 获取用户信息 */
getUserInfo: function(cb) {
    var that = this
    if (this.globalData.userInfo) {
        typeof cb == "function" && cb(this.globalData.userInfo)
    } else {
        //调用登录接口
        wx.login({
            success: function (res) {
                console.log(res)
                var code = res.code;
                //get wx user simple info
                wx.getUserInfo({
                    success: function (res) {
                        that.globalData.userInfo = res.userInfo                          
                 }
                });
            }
        });
    }
},
/** 登录 */
userLogin: function () {
    wx.redirectTo({
        url: '/pages/login/index'
    });
},
globalData: {
    userInfo: null
}
})