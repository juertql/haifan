// pages/user/top/index.js
var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
  showIndex:0,
  list:[],
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
     var that = this
     var showIndex = that.data.showIndex
      that.getTop(showIndex)
  },
  /** 获取数据 */
  getTop:function(index){
      var that = this
      wx.showLoading({
          title: '加载中',
      })
      var userId = wx.getStorageSync('userId')
      var types = ''
         if(index === 0){
             types = 'total'
         }
         if(index === 1){
           types = 'use'
         }
      wx.request({
          url: app.cg.hostUrl + '/user/getTop',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
              user_id: userId,
              types:types
          },
          success: function (res) {
              console.log(res)
              if (res.data.code == 200) {
                  var list = res.data.data
                  that.setData({
                     list: list
                  })
              }
          },
          complete:function(){
              wx.hideLoading()
          }
      })    
  },
  /** 切换 */
    swichNav:function(e){
        var that = this
        var index = e.target.dataset.current       
        that.setData({
            showIndex:Number(index)
        })
        that.getTop(Number(index))
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