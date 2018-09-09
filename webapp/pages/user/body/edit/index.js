// pages/addOrder/index.js
var app = getApp()
Page({

    /**
     * 页面的初始数据
     */
    data: {
       
        detail: [],
    },

    /**
     * 生命周期函数--监听页面加载
     */
    bindTypeChange: function (e) {
        console.log('picker发送选择改变，携带值为', e.detail.value)
        var types = this.data.goods_type;
        var index = e.detail.value;
        var id = types[index].id;

        this.setData({
            index: index,
            goods_type_id: id
        })
    },
    bindTimeChange: function (e) {
        console.log('picker发送选择改变，携带值为', e.detail.value)
        this.setData({
            give_food_time: e.detail.value
        })
    },
    onLoad: function (options) {
        var that = this
        var id = options.id;   
        that.orderDeatil(id)
       
    },
    /** 详情 */
    orderDeatil: function (id) {
        var that = this
        wx.showLoading({
            title: '加载中',
        })
        var userId = wx.getStorageSync('userId')
        var data = that.data
        wx.request({
            url: app.cg.hostUrl + '/user/getBodyDetail',
            method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
            header: {
                'content-type': 'application/json'
            },
            data: {
                user_id: userId,
                id: id,
            },
            success: function (res) {
                console.log(res)
                if (res.data.code == 200) {
                    var obj = res.data.data
                    that.setData({
                      
                        detail: obj,
                       
                    })
                    wx.hideLoading()
                }
                else {
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
   
    /** 提交 */
    formSubmit: function (e) {
        var that = this
        var adds = e.detail.value;
        var userId = wx.getStorageSync('userId')
        var data = that.data
        console.log(adds);
        wx.request({
            url: app.cg.hostUrl + '/user/editBody',
            method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
            header: {
                'content-type': 'application/json'
            },
            data: {
               adds,
               user_id:userId
            },
            success: function (res) {
                console.log(res);
                // success
                var status = res.data.code;
                if (status == 200) {
                    wx.showToast({
                        title: '修改成功！',
                        duration: 1000
                    });
                    wx.reLaunch({
                        url: '/pages/user/body/index',
                    })
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