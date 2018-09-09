// pages/user/body/list/index.js
var app = getApp()
import {
    getCurrentDate,
    generateDays,
    translateFormateDate,
    nextMonth,
    lastMonth,
    getDayMindex
} from '../../../goods/util'
Page({

  /**
   * 页面的初始数据
   */
  data: {
      data:[],
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
        var $this = this 
        $this.moreData()
     
  },
  moreData: function () {
      var that = this
      var userId = wx.getStorageSync('userId')
      wx.request({
          url: app.cg.hostUrl + '/User/getMoreBody',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
              user_id: userId
          },
          success: function (res) {
              console.log(res)
              if (res.data.code == 200) {
                  let data = res.data.data
                  that.setData({
                      data:data
                  })
              }
              else {
                  wx.showToast({
                      title: '无更多数据',
                      duration: 1000
                  });
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