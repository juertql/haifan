// pages/user/set/index.js
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
  
  },
  /** 提交 */
  formSubmit: function (e) {
      var that = this
      var adds = e.detail.value;
      var userId = wx.getStorageSync('userId')
      if(userId < 0){
          wx.reLaunch({
              url: '/pages/login/index',
          })
          return false;  
      }  
      var old_password = adds.old_password  
      var password = adds.password   
      var repassword = adds.repassword
      /** 检测数据 */   
      if (old_password == '' || password == '' || password == '' ){
          wx.showToast({
              title: '请完整输入',
              duration: 2000
          }); 
          return false;
      }
      if (password != repassword){
          wx.showToast({
              title: '密码不一致',
              duration: 2000
          });
          return false;  
      }
      wx.request({
          url: app.cg.hostUrl + '/user/setPassword',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
              user_id: userId,
              old_password:old_password,
              password:password

          },
          success: function (res) {            
              // success
              var status = res.data.code;
              if (status == 200) {
                  wx.showToast({
                      title: '修改成功！',
                      duration: 1000
                  });
                  try{
                      wx.removeStorageSync('userId')
                      wx.reLaunch({
                          url: '/pages/login/index',
                      })
                  } catch (e){
                      wx.showToast({
                          title: '网络异常！',
                          duration: 2000
                      });  
                  }
                  
              } else {
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