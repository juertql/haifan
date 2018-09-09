// pages/login/index.js
var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
  
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
      var aa = app.globalData.userInfo
      console.log(aa)
  },
  /** 提交 */
  formSubmit: function (e) {
      console.log('s')
      var adds = e.detail.value;
      var aa = app.globalData.userInfo
      wx.request({
          url: app.cg.hostUrl + '/login/login',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
              avatar: aa.avatarUrl,
              mobile:adds.mobile,
              password:adds.password
          },
          success: function (res) {
              console.log(res)
              // success
              var status = res.data.code;
              if (status == 200) {
                  //收货地址
                  wx.setStorageSync('userId', res.data.data.user_id);
                  wx.setStorageSync('address', res.data.data.address) ;    
                  wx.setStorageSync('name', res.data.data.name);   
                  wx.setStorageSync('mobile', res.data.data.mobile); 
                  wx.setStorageSync('remark', res.data.data.remark);     
                                          app.getGiftList()
                  app.checkGiftChance()
                  wx.showToast({
                      title: '登录成功！',
                      duration: 1000
                  });
                  
              } else {
                  wx.showToast({
                      title: res.data.msg,
                      duration: 1000
                  });
              }
              if(status == 200){
                  wx.reLaunch({
                      url: '/pages/index/index'
                  })
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
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
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
  
  },

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