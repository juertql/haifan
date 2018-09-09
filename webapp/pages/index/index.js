// pages/index/index.js
var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
      goods_type: [],
      indicatorDots: true,
      vertical: false,
      autoplay: true,
      interval: 1500,
      duration: 1000,
      currentTab: 0,
      circular:true,
      activity:[],
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
      var that = this
      this.getType()
      this.getActivity(false)
  },
  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
     
      var that = this
      this.getType()
      this.getActivity(true)
      

  }
    ,
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
  /** 活动 */
  getActivity: function (a) {
      var that = this
      wx.showLoading({
          title: '加载中',
      })
      wx.request({
          url: app.cg.hostUrl + '/goods/getActivity',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
        
          },
          success: function (res) {
              console.log(res)
              if (res.data.code == 200) {
                  let data = res.data.data
                  that.setData({
                      activity: data,

                  })
              }
          },
          complete: function () {
              wx.hideLoading()
              if (a == true) {
                  wx.stopPullDownRefresh()
              }
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