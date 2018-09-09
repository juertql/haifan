// pages/addOrder/index.js
var app = getApp()
Page({

    /**
     * 页面的初始数据
     */
    data: {
        goods_type: [
            
        ],
        goods_type_id: 0,
        goods_type_name: '',
        give_food_time: '',
        index: '',
        sn: '',
        address: '',
        detail:[],
        year_months_day:'',
        name:'',
        mobile:'',
        remark:'',
        address2:'',
        region: [],
        flag:true,
        flagArea:2,
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
        var sn = options.sn;
        var year_months_day = options.year_months_day;
        var address = wx.getStorageSync('address')
        var flag = options.flag == 'true' ? false : true
        that.getSet()
        that.setData({
            flag:flag,
            sn: sn,
            address: address,
            year_months_day: year_months_day,
        })
        that.getType()
        that.orderDeatil(sn)
        console.log(options)
    },
    //set
    getSet: function () {
        var that = this
        wx.request({
            url: app.cg.hostUrl + '/order/setArea',
            method: 'get',
            data: {
            },
            success: function (res) {
                console.log(res)
                if (res.data.code == 200) {
                    let data = res.data.data
                    that.setData({
                        flagArea: data,
                    })
                }
            }
        })
    },
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
    /** 查找类型下标 */
    findTypeIndex:function(id){
        var that = this
        var type = this.data.goods_type
        var length = type.length
        if(length > 0){
            for(let i = 0 ; i < length ; i++){
                if(type[i]['id'] == id){
                    that.setData({
                        index:i
                    })
                }
            }
        }
    },
    /** 订单详情 */
    orderDeatil: function (sn) {
        var that = this
        wx.showLoading({
            title: '加载中',
        })
        var userId = wx.getStorageSync('userId')
        var data = that.data
        wx.request({
            url: app.cg.hostUrl + '/order/getOrderDetail',
            method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
            header: {
                'content-type': 'application/json'
            },
            data: {
                user_id: userId,
                sn: sn,
            },
            success: function (res) {
                console.log(res)
                if (res.data.code == 200) {
                    var obj = res.data.data
                    that.setAddress(obj.address)
                    that.setData({
                        //give_food_time:obj.give_food_time,                       
                        address:obj.address,
                        detail:obj,
                        goods_type_id:obj.goods_type_id,
                        remark:obj.remark,
                        address2:obj.address2,
                    })
                    that.findTypeIndex(obj.goods_type_id)
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
            },
            complete:function(){
                wx.hideLoading()
            }
        })
    },
    setAddress: function (address) {
        var that = this

        address = String(address)
        if (address.indexOf('市') != '-1' && address.indexOf('区') != '-1' && address.indexOf('省')) {
            var aa = []
            aa[0] = String(address).substring(0, Number(address.indexOf('省')) + 1)
            aa[1] = String(address).substring(Number(address.indexOf('省')) + 1, Number(address.indexOf('市')) + 1)
            aa[2] = String(address).substring(Number(address.indexOf('市')) + 1, Number(address.indexOf('区')) + 1)
            var address2 = String(address).substr(Number(address.indexOf('区')) + 1)
            that.setData({
                region: aa,
                address2: address2
            })
        }
    },
    bindRegionChange: function (e) {
        console.log('picker发送选择改变，携带值为', e.detail.value)
        this.setData({
            region: e.detail.value
        })
    },
    /** 提交 */
    formSubmit: function (e) {
        var that = this
        var adds = e.detail.value;
        var userId = wx.getStorageSync('userId')
        var data = that.data
        var flag = that.data.flagArea
        if (flag == 2) {
            if (data.region[0] != '四川省' || data.region[1] != '成都市') {
                wx.showToast({
                    title: '暂只支持成都市',
                    duration: 1000
                });
                return false;
            }
        }
       
        wx.request({
            url: app.cg.hostUrl + '/order/edit',
            method: 'post', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
            header: {
                'content-type': 'application/json'
            },
            data: {
                sn:data.sn,
                user_id: userId,
                mobile: adds.mobile,
                name: adds.name,
                goods_type_id: data.goods_type_id,
                //give_food_time: data.give_food_time, 
                year_months_day: data.year_months_day,
                remark:adds.remark  ,
                address: data.region[0] + data.region[1] + data.region[2] + adds.address2,
                address2: adds.address2,
                address1: data.region          
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
                      url: '/pages/goods/index?year_months_day=' + data.year_months_day,
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