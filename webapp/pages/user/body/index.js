// pages/user/body/index.js
var app = getApp()
import {
    getCurrentDate,
    generateDays,
    translateFormateDate,
    nextMonth,
    lastMonth,
    getDayMindex
} from '../../goods/util'

Page({

  /**
   * 页面的初始数据
   */
  data: {
    detail:{},
    ym:'',
    img:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this 
    var currentDate = getCurrentDate()//年-月 && 年-月-日
    var year_month = currentDate.getYearMonth()
    var userId = wx.getStorageSync('userId')
    if (userId < 0) {
        wx.reLaunch({
            url: '/pages/login/index',
        })
        return false;
    }  
    console.log(year_month)
    that.setData({
        ym:year_month
    })
    that.getData(year_month,userId);
  },

  /** image */
  editImage:function(e){
      var $this = this
      var id = e.currentTarget.dataset.id
      var name = '重新上传'
       if(id == undefined){
           name = '上传照片'
         }
      wx.showActionSheet({
          itemList: [name],
          success: function (res) {
              var index = res.tapIndex
             if(index === 0){
                 //上传图片
                 $this.listenerButtonChooseImage(id)
             }
          },
          fail: function (res) {
              console.log(res.errMsg)
          }
      })
  },
  /**
  * 选择相册或者相机 配合上传图片接口用
  */
  listenerButtonChooseImage: function (id) {
      var that = this;
      var userId = wx.getStorageSync('userId')
      wx.chooseImage({
          count: 1,
          //original原图，compressed压缩图
          sizeType: ['original'],
          //album来源相册 camera相机 
          sourceType: ['camera', 'album'],
          //成功时会回调
          success: function (res) {
              var img = (res.tempFilePaths[0])
              //上传
               that.postImg(img,userId,id)
              //重绘视图
              that.setData({
                  img: res.tempFilePaths
              })
          }
      })
  },
  /** postImg */
  postImg:function(img,userid,id){
      var that = this
      wx.showLoading({
          title: '上传中',
      })
      wx.uploadFile({
          url: app.cg.hostUrl + '/User/editBodyImg',
          filePath: img,
          name: 'file',
          formData: {
              'user_id': encodeURI(userid),
              'id': encodeURI(id),
             
          },
          success: function (res) {
             
             if(res.data == 200){
                 wx.hideLoading()
                 wx.showToast({
                     title: '上传成功',
                     duration: 1000
                 });
                 setTimeout(that.toRefer,1000)
             }
             else{
                 wx.hideLoading()
                 wx.showToast({
                     title: '上传失败',
                     duration: 1000
                 });
             }
          }
      })
  },
  /**  */
  toRefer:function(){
      var $this = this
      var currentDate = getCurrentDate()//年-月 && 年-月-日
      var year_month = currentDate.getYearMonth()
      var userId = wx.getStorageSync('userId')
     wx.startPullDownRefresh({
         success:function(){
             $this.getData(year_month, userId);
             wx.stopPullDownRefresh()
         }
     })
  },
  moreData:function(){
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
                  wx.navigateTo({
                      url: '../body/list/index?data=' + data
                  })
              }
              else{
                  wx.showToast({
                      title: '无更多数据',
                      duration: 1000
                  });
              }
          }
      })
     
  },
  /** 编辑 */
  editData:function(e){
      var that = this
      var id = e.currentTarget.dataset.id
      wx.navigateTo({
          url: '../body/edit/index?id=' + id
      })
  },
  /** add */
  addData: function (e) {
      var that = this  
      wx.navigateTo({
          url: '../body/add/index'
      })
  },
  /** 分类 */
  getData: function (ym,userId) {
      var that = this
      wx.showLoading({
          title: '加载中',
      })
      wx.request({
          url: app.cg.hostUrl + '/User/getBody',
          method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
          header: {
              'content-type': 'application/json'
          },
          data: {
              ym: ym,
              user_id:userId
          },
          success: function (res) {
              console.log(res)
              if (res.data.code == 200) {
                  let data = res.data.data
                  that.setData({
                      detail: data,

                  })
                 
              }
              wx.hideLoading()
          }
          ,
          complete:function(){
              
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