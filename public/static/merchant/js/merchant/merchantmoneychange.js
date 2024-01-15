define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'merchant.merchantmoneychange/index',
        export_url: 'merchant.merchantmoneychange/export',
    };

    var Controller = {

        index: function () {
            ea.table.render({
                init: init,
                skin: 'row',
                even: true,
                modifyReload: false,
                height: 'full-40',
                toolbar: ['refresh'],
                defaultToolbar:['search'],
                cols: [[
                    {field: 'id', width: 110,search:false,title: ea.findText('编号')},
                    {field: 'type', width: 200,selectList: {1:ea.findText('订单收款'),
                            2:ea.findText('提款'),
                            3:ea.findText('提款手续费'),
                            4:ea.findText('余额变动'),
                            5:ea.findText('代付失败返回金额'),
                            6:ea.findText('代付失败返回手续费'),
                            8:ea.findText('ERC20提币手续费'),
                            9:ea.findText('商户补单')},title: ea.findText('类型'),templet: function (data){

                                return ea.findText(data.type)
                            }},
                    {field: 'before_money', width: 160,search:false,title: ea.findText('之前')},
                    {field: 'money', width: 160,search:false,title: ea.findText('变动')},
                    {field: 'after_money', width: 160,search:false, title: ea.findText('之后')},
                    {field: 'change_order_sn', width: 220,title: ea.findText('订单号')},
                    {field: 'change_order_id', width: 160,title: ea.findText('订单ID')},
                    {field: 'remark', width: 160,search:false,title: ea.findText('备注')},
                    {field: 'create_time', width:180, search:'range',title: ea.findText('时间')},
                ]],
            });

            ea.listen();
        },
    };
    return Controller;
});
