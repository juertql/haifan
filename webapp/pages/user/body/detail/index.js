// pages/user/body/index.js
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
        detail: {},
        ym: '',
        img: ''
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function (options) {
        var that = this
        var currentDate = getCurrentDate()//年-月 && 年-月-日
        var year_month = options.ym
        var userId = wx.getStorageSync('userId')
        if (userId < 0) {
            wx.reLaunch({
                url: '/pages/login/index',
            })
            return false;
        }
        that.setData({
            ym: year_month
        })
        that.getData(year_month, userId);
    },

   
  
    
    /**  */
    toRefer: function () {
        var $this = this
        var currentDate = getCurrentDate()//年-月 && 年-月-日
        var year_month = currentDate.getYearMonth()
        var userId = wx.getStorageSync('userId')
        wx.startPullDownRefresh({
            success: function () {
                $this.getData(year_month, userId);
                wx.stopPullDownRefresh()
            }
        })
    },

    /** 分类 */
    getData: function (ym, userId) {
        var that = this
        wx.request({
            url: app.cg.hostUrl + '/User/getBody',
            method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
            header: {
                'content-type': 'application/json'
            },
            data: {
                ym: ym,
                user_id: userId
            },
            success: function (res) {
                console.log(res)
                if (res.data.code == 200) {
                    let data = res.data.data
                    that.setData({
                        detail: data,

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