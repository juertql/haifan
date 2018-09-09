// ages/user/index.js
var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
      nav: [
          {
              name: "排行榜",
              icon: "icon-topbang top",
              id: 1,
              url:'./top/index'
          },
          {
              name: "身体档案",
              icon: "icon-stsj liwu",
              id: 2,
              url: './body/index'
          },
          {
              name: "修改密码",
              icon: "icon-xiugaimima1 xiugai",
              id: 3,
              url: './set/index'
          },
          {
              name: "退出登录",
              icon: "icon-tuichudenglu tuichu",
              id: 4,
              url: './top/index'
          }
      ],
      userInfo:[],
      hiddenOutModal:true,
      order_num:0,
      rest_num: 6,
      use_num: 4,
      total_num: 10,
      isRemindOrder:false
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
           var that = this
           /** 判断是否登录 */
           var userId = wx.getStorageSync('userId')
           if (userId <= 0) {
               this.userLogin()
           }
           var userinfo = app.globalData.userInfo
           console.log(userinfo)
           that.setData({
               userInfo:userinfo
           })
           this.getOrderCount(false)
  },
  /** 登录 */
  userLogin: function () {
      wx.redirectTo({
          url: '/pages/login/index'
      });
  },

  /** 获取选餐情况 */
  getOrderCount: function (a) {
    
      var that = this
      wx.showLoading({
          title: '加载中',
      })
      var userId = wx.getStorageSync('userId')
      wx.request({
          url: app.cg.hostUrl + '/order/count',
          method: 'post', // OPTIONS, GET, HEAD, POST, T, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
              user_id: userId,
          },
          success: function (res) {
              if (res.data.code == 200) {
                  let data = res.data.data
                  that.setData({
                      total_num: data.total,
                      rest_num: data.rest,
                      use_num: data.use,
                      order_num:data.order
                  }
                  )
                if (data.order <= 2 && data.rest > 0) {
                  that.setData(
                    {
                      isRemindOrder:true
                    }
                  )
                }else
                {
                  that.setData(
                    {
                      isRemindOrder: false
                    }
                  )
                }
              }
          },
          complete:function(){
              wx.hideLoading()
             if(a == true){
                 wx.stopPullDownRefresh()
             }
          }
      })
  },
  /** 退出登录 */
  loginout:function(){
      this.setData({
          hiddenOutModal:false
      })
  },
  /** 确认 */
  listenerOutConfirm:function(e){
      try {
          wx.removeStorageSync('userId')     
          wx.removeStorageSync('name')     
          wx.removeStorageSync('mobile') 
          wx.removeStorageSync('address')
          wx.removeStorageSync('remark')                  
              wx.showToast({
                  title: '退出成功！',
                  duration: 1000
              })         
          wx.reLaunch({
              url: '/pages/login/index',
          })
      }catch (e) {
          wx.showToast({
              title: '退出失败！',
              duration: 1000
          })
      }
      this.setData({
          hiddenOutModal: true 
      })
  },
  /** 取消 */
  listenerCancel:function(){
      this.setData({
          hiddenOutModal: true
      })
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    this.getOrderCount(false)
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  
  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
      var that = this
      /** 判断是否登录 */
      var userId = wx.getStorageSync('userId')
      if (userId <= 0) {
          this.userLogin()
      }
      var userinfo = app.globalData.userInfo

      that.setData({
          userInfo: userinfo
      })

      this.getOrderCount(true)


  }
    ,




    

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },
  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  }
})