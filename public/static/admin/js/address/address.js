define(["jquery", "easy-admin", "vue"], function ($, ea, Vue) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'address.address/index',
        add_url: 'address.address/add',
        gengxin_url: 'address.address/gengxin',
        delete_url: 'address.address/delete',
        export_url: 'address.address/export',
        modify_url: 'address.address/modify',
    };

    var form = layui.form;

    var Controller = {

        index: function () {
            ea.table.render({
                init: init,
                skin: 'row',
                even: true,
                modifyReload: false,
                toolbar: ['refresh',
                    [{
                        text: '添加',
                        url: init.add_url,
                        method: 'open',
                        auth: 'add',
                        class: 'layui-btn layui-btn-normal layui-btn-sm',
                        icon: 'icon-jia1 iconfont',
                    }],
                    'delete',
                    [{
                        text: '更新余额',
                        url: init.gengxin_url,
                        method: 'get',
                        field: 'id',
                        title: '确定更新余额吗？',
                        auth: 'gengxin',
                        class: 'layui-btn layui-btn-success layui-btn-sm',
                        icon: 'icon-redo-alt iconfont',
                        checkbox: true,
                    }],
                    ],
                cols: [[
                    {type: 'checkbox'},
                    {field: 'id', width: 80,search:false,title: 'id'},
                    {field: 'address', width: 360,title: '收款地址'},
                    {field: 'type', title: '地址类型', width: 100, selectList: {1: 'TRC20', 2: 'ERC20'},},
                    {field: 'img', width: 110,search:false,title: '地址图片', templet: ea.table.image},
                    {field: 'usdt_balance',width: 100, search:false,title: 'USDT余额'},
                    {field: 'trx_balance', width: 100,search:false,title: 'TRX余额'},
                    {field: 'eth_balance', width: 100,search:false,title: 'ETH余额'},
                    
                     
                    {field: 'status', title: '状态', width: 95, selectList: {1: '正常',0:'禁用'}, templet: ea.table.switch,tips:'正常|禁用'},
                    {field: 'create_time', width: 160, search:'range',title: '注册时间'},
                    {
                        
                        title: '操作',
                        width: 200,
                        templet: ea.table.tool,
                        operat: [
                            [
                            {
                                text: '更新余额',
                                url: init.gengxin_url,
                                method: 'get',
                                field: 'id',
                                title: '确定更新余额吗？',
                                auth: 'gengxin',
                                class: 'layui-btn layui-btn-xs layui-btn-success',
                            }, 
                            ],
                            'delete']
                    }
                    
                ]],
            });

            ea.listen();
        },
        add: function () {
            var app = new Vue({
                el: '#app-form',
                data: {
                    type: type
                }
            });

            form.on("radio(type)", function (data) {
                app.type = this.value;
            });
            ea.listen();
        },
        gengxin: function () {
            ea.listen();
        },
    };
    return Controller;
});