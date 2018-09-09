// pages/user/gift/index.js
var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
      giftList:[
          //{name:'精美礼品一份',create_time:'2017-10-10 10:11',order_status:1}
      ],
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
      var that = this
      that.getGiftList()
  },
/** 列表 */
getGiftList:function(){
    var that = this
    var userId = wx.getStorageSync('userId')

    wx.request({
        url: app.cg.hostUrl + '/user/getGiftList',
        method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
        header: {
            'content-type': 'application/json'
        },
        data: {
            user_id: userId,
        },
        success: function (res) {
            var flag = false
            if (res.data.code == 200) {
               var list = res.data.data
               that.setData({
                   giftList:list
               })
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