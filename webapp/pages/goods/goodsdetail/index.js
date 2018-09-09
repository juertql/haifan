// pages/goods/goodsdetail/index.js
var app = getApp()
var WxParse = require('../../../wxParse/wxParse.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
  detail:[],
  goods_id:'',
  flag:true,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
      var that = this
      var goods_id = options.id;
      that.getDetail(goods_id);
  },
  /** 详情 */
  getDetail: function (id) {
      var that = this
      wx.request({
          url: app.cg.hostUrl + '/goods/getAcDetail',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
             id:id
          },
          success: function (res) {
              console.log(res)
              if (res.data.code == 200) {
                  let data = res.data.data
                  that.setData({
                      detail: data,
                  })
                  WxParse.wxParse('content', 'html', data.content, that, 3)
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