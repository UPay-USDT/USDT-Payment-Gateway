define(["jquery", "tableSelect", "ckeditor"], function ($, tableSelect, undefined) {
    let  lang = document.cookie.includes('en-us')?'en-us':'zh-cn'
    var form = layui.form,
        layer = layui.layer,
        table = layui.table,
        laydate = layui.laydate,
        upload = layui.upload,
        element = layui.element,
        laytpl = layui.laytpl,
        tableSelect = layui.tableSelect;

    layer.config({
        skin: 'layui-layer-easy'
    });
    //todo： tipsMsg 定义一个obj 通过cookie判断语言，函数筛选出msg
    const langObj = {
        cn: {
            'echarts-1': '订单统计',
            'echarts-2': '订单',
            'echarts-3': '总量',
            'echarts-4': '',
            //order
            'order01': '补单',
            'order02': '编号',
            'order03': '订单金额',
            'order04': '商户订单号',
            'order05': '无风险：付款钱包地址几乎不可能与非法活动相关联',
            'order06': '查看',
            'order07': '低风险：付款钱包地址与非法活动相关的可能性低',
            'order08': '中风险：付款钱包地址与非法活动有相关的可能性',
            'order09': '高风险：付款钱包地址与非法活动相关的可能性高',
            'order10': '重大风险：付款钱包地址为参与非法活动的人',
            'order11': '手续费',
            'order12': '实际金额',
            'order13': '链路',
            'order14': '系统地址',
            'order16': '状态',
            'order17': '未支付',
            'order18': '已支付',
            'order19': '超时订单',
            'order20': '失败订单',
            'order21': '部分付款订单',
            'order22': '退款中',
            'order23': '部分退款',
            'order24': '退款失败',
            'order25': '部分付款',
            'order26': '退款成功',
            'order27': '商品名称',
            'order29': '商品描述',
            'order30': '订单创建时间',
            'order31': '支付时间',
            'order32': '商品描述',
            'withdraw01':'提款金额',
            'withdraw02':'收款地址',
            'withdraw03':'申请时间',
            'withdraw04':'处理时间',
            'withdraw05':'处理中',
            'withdraw06':'打款成功',
            'withdraw07':'打款失败',
            'withdraw08':'手动提现',
            'withdraw09':'API提现',
            'withdraw10':'自动提现',
            'withdraw11':'类型',
            'withdraw12':'链路类型',
            'withdraw13':'订单收款',
            'withdraw14':'提款',
            'withdraw15':'提款手续费',
            'withdraw16':'余额变动',
            'withdraw17':'代付失败返回金额',
            'withdraw18':'代付失败返回手续费',
            'withdraw20':'ERC20提币手续费',
            'withdraw21':'商户补单',
            'withdraw22':'之前',
            'withdraw23':'变动',
            'withdraw24':'之后',
            'withdraw25':'订单号',
            'withdraw26':'订单ID',
            'withdraw27':'备注',
            'withdraw28':'时间',
            'withdraw29':'日期',
            'withdraw30':'今日订单数',
            'withdraw31':'今日提款',
            'withdraw32':'今日订单金额',
            'withdraw33':'添加',
            'withdraw34':'收款地址',
            'withdraw35':'地址类型',
            'withdraw36':'地址二维码',
            'withdraw37':'正常',
            'withdraw38':'禁用',
            'withdraw39':'创建时间',
            'withdraw40':'更新时间',
            'withdraw41':'正常|禁用',
            'withdraw42':'操作',
            'withdraw73':'商户后台',
            'withdraw74':'无风险',
            'withdraw75':'低风险',
            'withdraw76':'中风险',
            'withdraw77':'高风险',
            'withdraw78':'重大风险',
             'withdraw79':'风险等级',
        },
        en:{
            'echarts-1': 'Order Statistics',
            'echarts-2': 'Order',
            'echarts-3': 'Total',
            'echarts-4': '',
            //order
            'order01': 'Single Supplement',
            'order02': 'Order ID',
            'order03': 'Payment Amount',
            'order04': 'Transaction ID',
            'order05': 'No risk: almost impossible to be associated with illegal activities',
            'order06': 'View',
            'order07': 'Low risk: the low possibility of being associated with illegal activities',
            'order08': 'Medium risk: the medium probability of being associated with illegal activities',
            'order09': 'High risk: high probability of being associated with illegal activities',
            'order10': 'Critical Risk: Those who are involved in illegal activities',
            'order11': 'Fee',
            'order12': 'Actual Amount',
            'order13': 'Chain',
            'order14': 'System Address',
            'order16': 'Status',
            'order17': 'Unpaid',
            'order18': 'Paid',
            'order19': 'Timeout Order',
            'order20': 'Failed Order',
            'order21': 'Part Payment Order',
            'order22': 'Refunding',
            'order23': 'Partial Refund',
            'order24': 'Refund Failed',
            'order25': 'Partial Payment',
            'order26': 'Refund Successfully',
            'order27': 'Product Name',
            'order29': 'Product Description',
            'order30': 'Payment Creation Time',
            'order31': 'Payment Time',
            'order32': 'Product description',
            'withdraw01':'Withdrawal Amount',
            'withdraw02':'Receive Wallet Address',
            'withdraw03':'Application time',
            'withdraw04':'Processing time',
            'withdraw05':'Processing',
            'withdraw06':'Successful payment',
            'withdraw07':'Payment failed',
            'withdraw08':'Manual withdrawal',
            'withdraw09':'API withdrawal',
            'withdraw10':'Automatic withdrawal',
            'withdraw11':'Type',
            'withdraw12':'Chain',
            'withdraw13':'Amount Received',
            'withdraw14':'Withdraw',
            'withdraw15':'Withdrawal fee',
            'withdraw16':'Balance Change',
            'withdraw17':'Withdrawal failed to return the amount',
            'withdraw18':'Withdrawal failed to return fee',
            'withdraw20':'ERC20 transfer fee',
            'withdraw21':'Single Supplement',
            'withdraw22':'Before',
            'withdraw23':'Change',
            'withdraw24':'After',
            'withdraw25':'Transaction ID',
            'withdraw26':'Order ID',
            'withdraw27':'Remark',
            'withdraw28':'Time',
            'withdraw29':'Date',
            'withdraw30':'Today Transaction Quantity',
            'withdraw31':'Withdraw today',
            'withdraw32':'Today Transaction Amount',
            'withdraw33':'Add',
            'withdraw34':'Receive Wallet Address',
            'withdraw35':'Chain',
            'withdraw36':'Address QR code',
            'withdraw37':'On',
            'withdraw38':'Off',
            'withdraw39':'Creation time',
            'withdraw40':'Update time',
            'withdraw41':'On|Off',
            'withdraw42':'Operate',
            'withdraw73':'Merchant',
            'withdraw74':'No Risk',
            'withdraw75':'Low Risk',
            'withdraw76':'Medium Risk',
            'withdraw77':'High Risk',
            'withdraw78':'Critical Risk',
            'withdraw79':'Risk Level',

        }
    };
    const adminObj = {
        cn:{
            'withdraw43':'返回数据格式有误',
            'withdraw44':'请求地址不能为空',
            'withdraw45':'加载中',
            'withdraw46':'请稍后再试',
            'withdraw47':'操作确认',
            'withdraw48':'确认',
            'withdraw49':'取消',
            'withdraw50':'添加',
            'withdraw51':'删除',
            'withdraw52':'导出',
            'withdraw53':'请输入',
            'withdraw54':'全部',
            'withdraw55':'条件搜索',
            'withdraw56':'搜 索',
            'withdraw57':'重 置',
            'withdraw58':'编辑',
            'withdraw59':'编辑信息',
            'withdraw60':'确定删除',
            'withdraw61':'开|关',
            'withdraw62':'点击行内容可以进行修改',
            'withdraw63':'请勾选需要操作的数据',
            'withdraw64':'确定进行该操作',
            'withdraw65':'根据查询进行导出，确定导出',
            'withdraw66':'请勾选需要删除的数据',
            'withdraw67':'请输入文件名',
            'withdraw68':'图片信息',
            'withdraw69':'文件原名',
            'withdraw70':'mime类型',
            'withdraw71':'选择成功',
            'withdraw72':'下拉选择字段有误',
            'withdraw73':'商户后台',
            'withdraw74':'文件原名',
            'withdraw75':'文件原名',
            'withdraw76':'文件原名',
            'withdraw77':'文件原名',
            'withdraw78':'文件原名',
            'withdraw79':'文件原名',
            'withdraw80':'文件原名',
        },
        en:{
           'withdraw43':'The returned data format is wrong',
            'withdraw44':'Request URL cannot be empty',
            'withdraw45':'Loading',
            'withdraw46':'Please try again later',
            'withdraw47':'Operation Confirmation',
            'withdraw48':'OK',
            'withdraw49':'Cancel',
            'withdraw50':'Add',
            'withdraw51':'Delete',
            'withdraw52':'Export',
            'withdraw53':'Please enter ',
            'withdraw54':'All',
            'withdraw55':'Filter',
            'withdraw56':'Search',
            'withdraw57':'Reset',
            'withdraw58':'Edit',
            'withdraw59':'Edit Information',
            'withdraw60':'Confirm Delete',
            'withdraw61':'On|Off',
            'withdraw62':'Click on the content to modify',
            'withdraw63':'Please tick the data that needs to be operated',
            'withdraw64':'OK to do this',
            'withdraw65':'Export according to the query, confirm the export',
            'withdraw66':'Please tick the data that needs to be deleted',
            'withdraw67':'Please enter a file name',
            'withdraw68':'Image information',
            'withdraw69':'Original file name',
            'withdraw70':'Mime type',
            'withdraw71':'Choose success',
            'withdraw72':'Wrong dropdown select field',
        }
    }
    const findInnerText = (text)=>{
        switch (text){
            case '返回数据格式有误':
                text = lang === 'en-us'?adminObj.en['withdraw43']:adminObj.cn['withdraw43']
                break
            case '搜索':
                text = lang === 'en-us'?'Search':'搜索'
                break

            case '请求地址不能为空':
                text = lang === 'en-us'?adminObj.en['withdraw44']:adminObj.cn['withdraw44']
                break
            case '加载中':
                text = lang === 'en-us'?adminObj.en['withdraw45']:adminObj.cn['withdraw45']
                break
            case '请稍后再试':
                text = lang === 'en-us'?adminObj.en['withdraw46']:adminObj.cn['withdraw46']
                break
            case '操作确认':
                text = lang === 'en-us'?adminObj.en['withdraw47']:adminObj.cn['withdraw47']
                break
            case '确认':
                text = lang === 'en-us'?adminObj.en['withdraw48']:adminObj.cn['withdraw48']
                break
            case '取消':
                text = lang === 'en-us'?adminObj.en['withdraw49']:adminObj.cn['withdraw49']
                break
            case '添加':
                text = lang === 'en-us'?adminObj.en['withdraw50']:adminObj.cn['withdraw50']
                break
            case '删除':
                text = lang === 'en-us'?adminObj.en['withdraw51']:adminObj.cn['withdraw51']
                break
            case '导出':
                text = lang === 'en-us'?adminObj.en['withdraw52']:adminObj.cn['withdraw52']
                break
            case '请输入':

                text = lang === 'en-us'?adminObj.en['withdraw53']:adminObj.cn['withdraw53']
                break
            case '全部':
                text = lang === 'en-us'?adminObj.en['withdraw54']:adminObj.cn['withdraw54']
                break
            case '条件搜索':
                text = lang === 'en-us'?adminObj.en['withdraw55']:adminObj.cn['withdraw55']
                break
            case '搜 索':
                text = lang === 'en-us'?adminObj.en['withdraw56']:adminObj.cn['withdraw56']
                break
            case '重 置':
                text = lang === 'en-us'?adminObj.en['withdraw57']:adminObj.cn['withdraw57']
                break
            case '编辑':
                text = lang === 'en-us'?adminObj.en['withdraw58']:adminObj.cn['withdraw58']
                break
            case '编辑信息':
                text = lang === 'en-us'?adminObj.en['withdraw59']:adminObj.cn['withdraw59']
                break
            case '确定删除':
                text = lang === 'en-us'?adminObj.en['withdraw60']:adminObj.cn['withdraw60']
                break
            case '开|关':
                text = lang === 'en-us'?adminObj.en['withdraw61']:adminObj.cn['withdraw61']
                break
            case '点击行内容可以进行修改':
                text = lang === 'en-us'?adminObj.en['withdraw62']:adminObj.cn['withdraw62']
                break
            case '请勾选需要操作的数据':
                text = lang === 'en-us'?adminObj.en['withdraw63']:adminObj.cn['withdraw63']
                break
            case '确定进行该操作':
                text = lang === 'en-us'?adminObj.en['withdraw64']:adminObj.cn['withdraw64']
                break
            case '根据查询进行导出，确定导出':
                text = lang === 'en-us'?adminObj.en['withdraw65']:adminObj.cn['withdraw65']
                break
            case '请勾选需要删除的数据':
                text = lang === 'en-us'?adminObj.en['withdraw66']:adminObj.cn['withdraw66']
                break
            case '请输入文件名':
                text = lang === 'en-us'?adminObj.en['withdraw67']:adminObj.cn['withdraw67']
                break
            case '图片信息':
                text = lang === 'en-us'?adminObj.en['withdraw68']:adminObj.cn['withdraw68']
                break
            case '文件原名':
                text = lang === 'en-us'?adminObj.en['withdraw69']:adminObj.cn['withdraw69']
                break
            case 'mime类型':
                text = lang === 'en-us'?adminObj.en['withdraw70']:adminObj.cn['withdraw70']
                break
            case '选择成功':
                text = lang === 'en-us'?adminObj.en['withdraw71']:adminObj.cn['withdraw71']
                break
            case '下拉选择字段有误':
                text = lang === 'en-us'?adminObj.en['withdraw72']:adminObj.cn['withdraw72']
                break
        }
        return text
    }
   const msgObj = {
    cn:{
        "tip1":"加载中",
        "tip2":"保存成功",
        "tip3":"商户不存在",
        "tip4":"退出登录成功",
        "tip5":"验证码错误!",
        "tip6":"收款地址不是TRC地址",
        "tip7":"密码输入有误",
        "tip8":"收款地址不是ERC地址",
        "tip9":"USDT余额不足",
        "tip10":"自动提现金额不能低于10",
        "tip11":"生成成功",
        "tip12":"地址格式错误",
        "tip13":"删除成功",
        "tip14":"请稍后再试",
        "tip15":"金额不能低于1",
        "tip16":"返回数据格式有误",
        "tip17":"登录成功",
        "tip18":"自动提现金额不能超过10000",
        "tip19":"IP格式错误",
        "tip20":"绑定成功",
        "tip21":"交易失败或不存在",
        "tip22":"两次密码输入不一致",
        "tip23":"代付单笔金额不能低于1",
        "tip24":"代付单笔金额不能超过10000",
        "tip25":"TRC收款地址错误",
        "tip26":'谷歌验证码错误！',
        "tip27":'请填写验证码',
        "tip28":'请填写TRC自动提现地址',
        "tip29":'验证码不能为空',
        "tip30":'收款地址不是ETH地址',
        "tip31":'地址已存在',
        "tip32":'商户名称已存在',
        "tip33":'注册成功，去登录',
        "tip34":'交易哈希已存在',
        "tip35":'',
        "tip36":'',
        "tip37":'',
    },
    en:{
        "tip1":"Loading",
        "tip2":"Saved Successfully",
        "tip3":"Merchant does not exist",
        "tip4":"Logged Out",
        "tip5":"Verification code error",
        "tip6":"The receiving address is not a TRC address",
        "tip7":"Wrong password",
        "tip8":"The receiving address is not a ERC address",
        "tip9":"Insufficient USDT balance",
        "tip10":"The automatic withdrawal amount cannot be less than 10",
        "tip11":"Generated Successfully",
        "tip12":"Wrong Address Format",
        "tip13":"Successfully Deleted",
        "tip14":"Please try again later",
        "tip15":"Amount cannot be less than 1",
        "tip16":"The returned data format is wrong",
        "tip17":"Login Successful",
        "tip18":" The automatic withdrawal amount ",
        "tip19":" IP format error",
        "tip20":"Bind successfully",
        "tip21":"Transaction failed or does not exist",
        "tip22":"The two passwords entered are inconsistent",
        "tip23":"The single payment amount cannot be less than 1",
        "tip24":"The single payment amount cannot exceed 10000",
        "tip25":"Wrong TRC receiving address",
        "tip26":'Google verification code error!',
        "tip27":'Please enter verification code',
        "tip28":'Please fill in the TRC automatic withdrawal address',
         "tip29":'Verification code must be filled',
        "tip30":'The receiving address is not a ERC address',
        "tip31":'Address already exists',
        "tip32":'Merchant name already exists',
        "tip33":'Registered successfully, go to log in',
        "tip34":'Transaction hash already exists',
        "tip35":'',
        "tip36":'',
        "tip37":'',
    }
}
    const findMsgText = (text)=>{
        switch (text){
            case '交易哈希已存在':
                text = lang === 'en-us'?msgObj.en['tip34']:msgObj.cn['tip34']
                break
            case '注册成功，去登录':
                text = lang === 'en-us'?msgObj.en['tip33']:msgObj.cn['tip33']
                break
            case '商户名称已存在':
                text = lang === 'en-us'?msgObj.en['tip32']:msgObj.cn['tip32']
                break
            case '地址已存在':
                text = lang === 'en-us'?msgObj.en['tip31']:msgObj.cn['tip31']
                break
            case '验证码不能为空':
                text = lang === 'en-us'?msgObj.en['tip29']:msgObj.cn['tip29']
                break
            case '收款地址不是ETH地址':
                text = lang === 'en-us'?msgObj.en['tip30']:msgObj.cn['tip30']
                break
            case '谷歌验证码错误！':
                text = lang === 'en-us'?msgObj.en['tip26']:msgObj.cn['tip26']
                break
            case '请填写TRC自动提现地址':
                text = lang === 'en-us'?msgObj.en['tip28']:msgObj.cn['tip28']
                break
            case '验证码 require':
                text = lang === 'en-us'?msgObj.en['tip27']:msgObj.cn['tip27']
                break
             case '加载中':
                text = lang === 'en-us'?msgObj.en['tip1']:msgObj.cn['tip1']
                break
             case 'TRC收款地址错误':
                text = lang === 'en-us'?msgObj.en['tip25']:msgObj.cn['tip25']
                break
            case '交易失败或不存在':
                text = lang === 'en-us'?msgObj.en['tip21']:msgObj.cn['tip21']
                break
             case '代付单笔金额不能超过10000':
                text = lang === 'en-us'?msgObj.en['tip24']:msgObj.cn['tip24']
                break
             case '代付单笔金额不能低于1':
                text = lang === 'en-us'?msgObj.en['tip23']:msgObj.cn['tip23']
                break
            case '两次密码输入不一致':
                text = lang === 'en-us'?msgObj.en['tip22']:msgObj.cn['tip22']
                break
            case '保存成功':
                text = lang === 'en-us'?msgObj.en['tip2']:msgObj.cn['tip2']
                break
            case '绑定成功':
                text = lang === 'en-us'?msgObj.en['tip20']:msgObj.cn['tip20']
                break
            case '商户不存在':
                text = lang === 'en-us'?msgObj.en['tip3']:msgObj.cn['tip3']
                break
            case '退出登录成功':
                text = lang === 'en-us'?msgObj.en['tip4']:msgObj.cn['tip4']
                break
            case '验证码错误!':
                text = lang === 'en-us'?msgObj.en['tip5']:msgObj.cn['tip5']
                break
            case '收款地址不是TRC地址':
                text = lang === 'en-us'?msgObj.en['tip6']:msgObj.cn['tip6']
                break
            case '密码输入有误':
                text = lang === 'en-us'?msgObj.en['tip7']:msgObj.cn['tip7']
                break
            case '收款地址不是ERC地址':
                text = lang === 'en-us'?msgObj.en['tip8']:msgObj.cn['tip8']
                break
            case 'USDT余额不足':
                text = lang === 'en-us'?msgObj.en['tip9']:msgObj.cn['tip9']
                break
            case '自动提现金额不能低于10':
                text = lang === 'en-us'?msgObj.en['tip10']:msgObj.cn['tip10']
                break
            case '地址格式错误':
                text = lang === 'en-us'?msgObj.en['tip12']:msgObj.cn['tip12']
                break
            case '删除成功':
                text = lang === 'en-us'?msgObj.en['tip13']:msgObj.cn['tip13']
                break
            case '请稍后再试':
                text = lang === 'en-us'?msgObj.en['tip14']:msgObj.cn['tip14']
                break
            case '金额不能低于1':
                text = lang === 'en-us'?msgObj.en['tip15']:msgObj.cn['tip15']
                break
            case '返回数据格式有误':
                text = lang === 'en-us'?msgObj.en['tip16']:msgObj.cn['tip16']
                break
            case '登录成功':
                text = lang === 'en-us'?msgObj.en['tip17']:msgObj.cn['tip17']
                break
            case '自动提现金额不能超过10000':
                text = lang === 'en-us'?msgObj.en['tip18']:msgObj.cn['tip18']
                break
            case 'IP格式错误':
                text = lang === 'en-us'?msgObj.en['tip19']:msgObj.cn['tip19']
                break
        }
        return text
    }
    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        upload_url: 'ajax/upload',
        upload_exts: 'doc|gif|ico|icon|jpg|mp3|mp4|p12|pem|png|rar',
    };

    var admin = {
        findText(text){
            switch (text) {
                 case '风险等级':
                    text = lang === 'en-us'?langObj.en['withdraw79']:langObj.cn['withdraw79']
                    break
                case 'Low':
                    text = lang === 'en-us'?langObj.en['withdraw75']:langObj.cn['withdraw75']
                    break
                case 'Analyzing...':
                    text = lang === 'en-us'?langObj.en['withdraw75']:langObj.cn['withdraw75']
                    break
                case 'Medium':
                    text = lang === 'en-us'?langObj.en['withdraw76']:langObj.cn['withdraw76']
                    break
                case 'High':
                    text = lang === 'en-us'?langObj.en['withdraw77']:langObj.cn['withdraw77']
                    break
                case 'None':
                    text = lang === 'en-us'?langObj.en['withdraw74']:langObj.cn['withdraw74']
                    break
                case 'Critical':
                    text = lang === 'en-us'?langObj.en['withdraw78']:langObj.cn['withdraw78']
                    break




                case '商户后台':
                    text = lang === 'en-us'?langObj.en['withdraw73']:langObj.cn['withdraw73']
                    break
                case '订单统计':
                    text = lang === 'en-us'?langObj.en['echarts-1']:langObj.cn['echarts-1']
                    break
                case '订单':
                    text = lang === 'en-us'?langObj.en['echarts-2']:langObj.cn['echarts-2']
                    break
                case '总量':
                    text = lang === 'en-us'?langObj.en['echarts-3']:langObj.cn['echarts-3']
                    break
                case '补单':
                    text = lang === 'en-us'?langObj.en['order01']:langObj.cn['order01']
                    break
                case '编号':
                    text = lang === 'en-us'?langObj.en['order02']:langObj.cn['order02']
                    break
                case '订单金额':
                    text = lang === 'en-us'?langObj.en['order03']:langObj.cn['order03']
                    break
                case '商户订单号':
                    text = lang === 'en-us'?langObj.en['order04']:langObj.cn['order04']
                    break
                case '无风险：付款钱包地址几乎不可能与非法活动相关联':
                    text = lang === 'en-us'?langObj.en['order05']:langObj.cn['order05']
                    break
                case '查看':
                    text = lang === 'en-us'?langObj.en['order06']:langObj.cn['order06']
                    break
                case '低风险：付款钱包地址与非法活动相关的可能性低':
                    text = lang === 'en-us'?langObj.en['order07']:langObj.cn['order07']
                    break
                case '中风险：付款钱包地址与非法活动有相关的可能性':
                    text = lang === 'en-us'?langObj.en['order08']:langObj.cn['order08']
                    break
                case '高风险：付款钱包地址与非法活动相关的可能性高':
                    text = lang === 'en-us'?langObj.en['order09']:langObj.cn['order09']
                    break
                case '重大风险：付款钱包地址为参与非法活动的人':
                    text = lang === 'en-us'?langObj.en['order10']:langObj.cn['order10']
                    break
                case '手续费':
                    text = lang === 'en-us'?langObj.en['order11']:langObj.cn['order11']
                    break
                case '实际金额':
                    text = lang === 'en-us'?langObj.en['order12']:langObj.cn['order12']
                    break
                case '链路':
                    text = lang === 'en-us'?langObj.en['order13']:langObj.cn['order13']
                    break
                case '系统地址':
                    text = lang === 'en-us'?langObj.en['order14']:langObj.cn['order14']
                    break

                case '状态':
                    text = lang === 'en-us'?langObj.en['order16']:langObj.cn['order16']
                    break
                case '未支付':
                    text = lang === 'en-us'?langObj.en['order17']:langObj.cn['order17']
                    break
                case '已支付':
                    text = lang === 'en-us'?langObj.en['order18']:langObj.cn['order18']
                    break
                case '超时订单':
                    text = lang === 'en-us'?langObj.en['order19']:langObj.cn['order19']
                    break
                case '失败订单':
                    text = lang === 'en-us'?langObj.en['order21']:langObj.cn['order21']
                    break
                case '退款中':
                    text = lang === 'en-us'?langObj.en['order22']:langObj.cn['order22']
                    break
                case '部分退款':
                    text = lang === 'en-us'?langObj.en['order23']:langObj.cn['order23']
                    break
                case '退款失败':
                    text = lang === 'en-us'?langObj.en['order24']:langObj.cn['order24']
                    break
                case '部分付款':
                    text = lang === 'en-us'?langObj.en['order25']:langObj.cn['order25']
                    break
                case '退款成功':
                    text = lang === 'en-us'?langObj.en['order26']:langObj.cn['order26']
                    break
                case '商品名称':
                    text = lang === 'en-us'?langObj.en['order27']:langObj.cn['order27']
                    break
                case '商品描述':
                    text = lang === 'en-us'?langObj.en['order32']:langObj.cn['order32']
                    break
                case '订单创建时间':
                    text = lang === 'en-us'?langObj.en['order30']:langObj.cn['order30']
                    break
                case '支付时间':
                    text = lang === 'en-us'?langObj.en['order31']:langObj.cn['order31']
                    break
                case '代付失败返回手续费':
                    text = lang === 'en-us'?langObj.en['withdraw18']:langObj.cn['withdraw18']
                    break
                case '提款金额':
                    text = lang === 'en-us'?langObj.en['withdraw01']:langObj.cn['withdraw01']
                    break
                case '收款地址':
                    text = lang === 'en-us'?langObj.en['withdraw02']:langObj.cn['withdraw02']
                    break
                case '链':
                    text = lang === 'en-us'?langObj.en['withdraw12']:langObj.cn['withdraw12']
                    break
                case '申请时间':
                    text = lang === 'en-us'?langObj.en['withdraw03']:langObj.cn['withdraw03']
                    break
                case '处理时间':
                    text = lang === 'en-us'?langObj.en['withdraw04']:langObj.cn['withdraw04']
                    break
                case '处理中':
                    text = lang === 'en-us'?langObj.en['withdraw05']:langObj.cn['withdraw05']
                    break
                case '打款成功':
                    text = lang === 'en-us'?langObj.en['withdraw06']:langObj.cn['withdraw06']
                    break
                case '打款失败':
                    text = lang === 'en-us'?langObj.en['withdraw07']:langObj.cn['withdraw07']
                    break
                case '手动提现':
                    text = lang === 'en-us'?langObj.en['withdraw08']:langObj.cn['withdraw08']
                    break
                case 'API提现':
                    text = lang === 'en-us'?langObj.en['withdraw09']:langObj.cn['withdraw09']
                    break
                case '自动提现':
                    text = lang === 'en-us'?langObj.en['withdraw10']:langObj.cn['withdraw10']
                    break
                case '类型':
                    text = lang === 'en-us'?langObj.en['withdraw11']:langObj.cn['withdraw11']
                    break
                case '订单收款':
                    text = lang === 'en-us'?langObj.en['withdraw13']:langObj.cn['withdraw13']
                    break
                case '提款':
                    text = lang === 'en-us'?langObj.en['withdraw14']:langObj.cn['withdraw14']
                    break
                case '提款手续费':
                    text = lang === 'en-us'?langObj.en['withdraw15']:langObj.cn['withdraw15']
                    break
                case '余额变动':
                    text = lang === 'en-us'?langObj.en['withdraw16']:langObj.cn['withdraw16']
                    break
                case '代付失败返回金额':
                    text = lang === 'en-us'?langObj.en['withdraw17']:langObj.cn['withdraw17']
                    break
                case 'ERC20提币手续费':
                    text = lang === 'en-us'?langObj.en['withdraw20']:langObj.cn['withdraw20']
                    break
                case '商户补单':
                    text = lang === 'en-us'?langObj.en['withdraw21']:langObj.cn['withdraw21']
                    break
                case '之前':
                    text = lang === 'en-us'?langObj.en['withdraw22']:langObj.cn['withdraw22']
                    break
                case '变动':
                    text = lang === 'en-us'?langObj.en['withdraw23']:langObj.cn['withdraw23']
                    break
                case '之后':
                    text = lang === 'en-us'?langObj.en['withdraw24']:langObj.cn['withdraw24']
                    break
                case '订单号':
                    text = lang === 'en-us'?langObj.en['withdraw25']:langObj.cn['withdraw25']
                    break
                case '订单ID':
                    text = lang === 'en-us'?langObj.en['withdraw26']:langObj.cn['withdraw26']
                    break
                case '备注':
                    text = lang === 'en-us'?langObj.en['withdraw27']:langObj.cn['withdraw27']
                    break
                case '日期':
                    text = lang === 'en-us'?langObj.en['withdraw29']:langObj.cn['withdraw29']
                    break
                case '时间':
                    text = lang === 'en-us'?langObj.en['withdraw28']:langObj.cn['withdraw28']
                    break
                case '今日订单数':
                    text = lang === 'en-us'?langObj.en['withdraw30']:langObj.cn['withdraw30']
                    break
                case '今日提款':
                    text = lang === 'en-us'?langObj.en['withdraw31']:langObj.cn['withdraw31']
                    break
                case '今日订单金额':
                    text = lang === 'en-us'?langObj.en['withdraw32']:langObj.cn['withdraw32']
                    break
                case '添加':
                    text = lang === 'en-us'?langObj.en['withdraw33']:langObj.cn['withdraw33']
                    break
                case '收款地址':
                    text = lang === 'en-us'?langObj.en['withdraw34']:langObj.cn['withdraw34']
                    break
                case '地址类型':
                    text = lang === 'en-us'?langObj.en['withdraw35']:langObj.cn['withdraw35']
                    break
                case '地址二维码':
                    text = lang === 'en-us'?langObj.en['withdraw36']:langObj.cn['withdraw36']
                    break
                case '正常':
                    text = lang === 'en-us'?langObj.en['withdraw37']:langObj.cn['withdraw37']
                    break
                case '禁用':
                    text = lang === 'en-us'?langObj.en['withdraw38']:langObj.cn['withdraw38']
                    break
                case '创建时间':
                    text = lang === 'en-us'?langObj.en['withdraw39']:langObj.cn['withdraw39']
                    break
                case '更新时间':
                    text = lang === 'en-us'?langObj.en['withdraw40']:langObj.cn['withdraw40']
                    break
                case '正常|禁用':
                    text = lang === 'en-us'?langObj.en['withdraw41']:langObj.cn['withdraw41']
                    break
                case '操作':
                    text = lang === 'en-us'?langObj.en['withdraw42']:langObj.cn['withdraw42']
                    break






            }
            return text
        },
        config: {
            shade: [0.02, '#000'],
        },
        url: function (url) {
            return '/' + CONFIG.ADMIN + '/' + url;
        },
        checkAuth: function (node, elem) {
            if (CONFIG.IS_SUPER_ADMIN) {
                return true;
            }
            if ($(elem).attr('data-auth-' + node) === '1') {
                return true;
            } else {
                return false;
            }
        },
        parame: function (param, defaultParam) {
            return param !== undefined ? param : defaultParam;
        },
        request: {
            post: function (option, ok, no, ex) {
                return admin.request.ajax('post', option, ok, no, ex);
            },
            get: function (option, ok, no, ex) {
                return admin.request.ajax('get', option, ok, no, ex);
            },
            ajax: function (type, option, ok, no, ex) {
                type = type || 'get';
                option.url = option.url || '';
                option.data = option.data || {};
                option.prefix = option.prefix || false;
                option.statusName = option.statusName || 'code';
                option.statusCode = option.statusCode || 1;
                ok = ok || function (res) {
                };
                no = no || function (res) {
                    var msg = res.msg == undefined ? findInnerText('返回数据格式有误') : res.msg;
                    admin.msg.error(msg);
                    return false;
                };
                ex = ex || function (res) {
                };
                if (option.url == '') {
                    admin.msg.error(findInnerText('请求地址不能为空'));
                    return false;
                }
                if (option.prefix == true) {
                    option.url = admin.url(option.url);
                }
                var index = admin.msg.loading(findInnerText('加载中'));
                $.ajax({
                    url: option.url,
                    type: type,
                    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                    dataType: "json",
                    data: option.data,
                    timeout: 60000,
                    success: function (res) {
                        admin.msg.close(index);
                        if (eval('res.' + option.statusName) == option.statusCode) {
                            return ok(res);
                        } else {
                            return no(res);
                        }
                    },
                    error: function (xhr, textstatus, thrown) {
                        admin.msg.error('Status:' + xhr.status + '，' + xhr.statusText + '，' + findInnerText('请稍后再试')+'！', function () {
                            ex(this);
                        });
                        return false;
                    }
                });
            }
        },
        common: {
            parseNodeStr: function (node) {
                var array = node.split('/');
                $.each(array, function (key, val) {
                    if (key === 0) {
                        val = val.split('.');
                        $.each(val, function (i, v) {
                            val[i] = admin.common.humpToLine(v.replace(v[0], v[0].toLowerCase()));
                        });
                        val = val.join(".");
                        array[key] = val;
                    }
                });
                node = array.join("/");
                return node;
            },
            lineToHump: function (name) {
                return name.replace(/\_(\w)/g, function (all, letter) {
                    return letter.toUpperCase();
                });
            },
            humpToLine: function (name) {
                return name.replace(/([A-Z])/g, "_$1").toLowerCase();
            },
        },
        msg: {
            // 成功消息
            success: function (msg, callback) {
                if (callback === undefined) {
                    callback = function () {
                    }
                }
                var index = layer.msg(findMsgText(msg), {icon: 1, shade: admin.config.shade, scrollbar: false, time: 2000, shadeClose: true}, callback);
                return index;
            },
            // 失败消息
            error: function (msg, callback) {
                if (callback === undefined) {
                    callback = function () {
                    }
                }
                var index = layer.msg(findMsgText(msg), {icon: 2, shade: admin.config.shade, scrollbar: false, time: 3000, shadeClose: true}, callback);
                return index;
            },
            // 警告消息框
            alert: function (msg, callback) {
                var index = layer.alert(findMsgText(msg), {end: callback, time:3000,scrollbar: false});
                return index;
            },
            // 对话框
            confirm: function (msg, ok, no) {
                var index = layer.confirm(findMsgText(msg), {title: findInnerText(''), btn: [findInnerText('确认'), findInnerText('取消')]}, function () {
                    typeof ok === 'function' && ok.call(this);
                }, function () {
                    typeof no === 'function' && no.call(this);
                    self.close(index);
                });
                return index;
            },
            // 消息提示
            tips: function (msg, time, callback) {
                var index = layer.msg(findMsgText(msg), {time: (time || 3) * 3000, shade: this.shade, end: callback, shadeClose: true});
                return index;
            },
            // 加载中提示
            loading: function (msg, callback) {
                var index = msg ? layer.msg(findMsgText(msg), {icon: 16, scrollbar: false, shade: this.shade, time: 0, end: callback}) : layer.load(2, {time: 0, scrollbar: false, shade: this.shade, end: callback});
                return index;
            },
            // 关闭消息框
            close: function (index) {
                return layer.close(index);
            }
        },
        table: {
            render: function (options) {
                options.init = options.init || init;
                options.modifyReload = admin.parame(options.modifyReload, true);
                options.elem = options.elem || options.init.table_elem;
                options.id = options.id || options.init.table_render_id;
                options.layFilter = options.id + '_LayFilter';
                options.url = options.url || admin.url(options.init.index_url);
                options.page = admin.parame(options.page, true);
                options.search = admin.parame(options.search, true);
                options.skin = options.skin || 'line';
                options.limit = options.limit || 15;
                options.limits = options.limits || [10, 15, 20, 25, 50, 100];
                options.cols = options.cols || [];
                // options.defaultToolbar = options.defaultToolbar || ['filter', 'print'] ;
                options.defaultToolbar = options.defaultToolbar || ['filter', 'print'] ;

                if (options.search) {
                    options.defaultToolbar.push({
                        title: findInnerText('搜索'),
                        layEvent: 'TABLE_SEARCH',
                        icon: 'layui-icon-search',
                        extend: 'data-table-id="' + options.id + '"'
                    });
                }

                // 判断是否为移动端
                if (admin.checkMobile()) {
                    options.defaultToolbar = !options.search ? ['filter'] : ['filter', {
                        title: findInnerText('搜索'),
                        layEvent: 'TABLE_SEARCH',
                        icon: 'layui-icon-search',
                        extend: 'data-table-id="' + options.id + '"'
                    }];
                }

                // 判断元素对象是否有嵌套的
                options.cols = admin.table.formatCols(options.cols, options.init);

                // 初始化表格lay-filter
                $(options.elem).attr('lay-filter', options.layFilter);

                // 初始化表格搜索
                if (options.search === true) {
                    admin.table.renderSearch(options.cols, options.elem, options.id);
                }

                // 初始化表格左上方工具栏
                options.toolbar = options.toolbar || ['refresh', 'add', 'delete'];
                options.toolbar = admin.table.renderToolbar(options.toolbar, options.elem, options.id, options.init);

                // 判断是否有操作列表权限
                options.cols = admin.table.renderOperat(options.cols, options.elem);

                // 初始化表格
                var newTable = table.render(options);

                // 监听表格搜索开关显示
                admin.table.listenToolbar(options.layFilter, options.id);

                // 监听表格开关切换
                admin.table.renderSwitch(options.cols, options.init, options.id, options.modifyReload);

                // 监听表格开关切换
                admin.table.listenEdit(options.init, options.layFilter, options.id, options.modifyReload);

                return newTable;
            },
            renderToolbar: function (data, elem, tableId, init) {
                data = data || [];
                var toolbarHtml = '';
                $.each(data, function (i, v) {
                    if (v === 'refresh') {
                        toolbarHtml += ' <button class="layui-btn layui-btn-sm layuimini-btn-primary" data-table-refresh="' + tableId + '"><i class="icon-icon-refresh iconfont"></i> </button>\n';
                    } else if (v === 'add') {
                        if (admin.checkAuth('add', elem)) {
                            toolbarHtml += '<button class="layui-btn layui-btn-normal layui-btn-sm" data-open="' + init.add_url + '" data-title="添加"><i class="iconfont icon-plus" style="color:#fff"></i>'+  findInnerText('添加')+'</button>\n';
                        }
                    } else if (v === 'delete') {
                        if (admin.checkAuth('delete', elem)) {
                            toolbarHtml += '<button class="layui-btn layui-btn-sm layui-btn-danger" style="background:#F53C4B" data-url="' + init.delete_url + '" data-table-delete="' + tableId + '"><i class="icon-trash-alt-solid iconfont" style="color:#fff"></i> '+  findInnerText('删除')+'</button>\n';
                        }
                    } else if (typeof v === "object") {
                        $.each(v, function (ii, vv) {
                            vv.class = vv.class || '';
                            vv.icon = vv.icon || '';
                            vv.auth = vv.auth || '';
                            vv.url = vv.url || '';
                            vv.method = vv.method || 'open';
                            vv.title = vv.title || vv.text;
                            vv.text = vv.text || vv.title;
                            vv.extend = vv.extend || '';
                            vv.checkbox = vv.checkbox || false;
                            if (admin.checkAuth(vv.auth, elem)) {
                                toolbarHtml += admin.table.buildToolbarHtml(vv, tableId);
                            }
                        });
                    }
                });
                return '<div>' + toolbarHtml + '</div>';
            },
            renderSearch: function (cols, elem, tableId) {
                // TODO 只初始化第一个table搜索字段，如果存在多个(绝少数需求)，得自己去扩展
                cols = cols[0] || {};
                var newCols = [];
                var formHtml = '';
                $.each(cols, function (i, d) {
                    d.field = d.field || false;
                    d.fieldAlias = admin.parame(d.fieldAlias, d.field);
                    d.title = d.title || d.field || '';
                    d.selectList = d.selectList || {};
                    d.search = admin.parame(d.search, true);
                    d.searchTip = d.searchTip || findInnerText('请输入') + d.title || '';
                    d.searchValue = d.searchValue || '';
                    d.searchOp = d.searchOp || '%*%';
                    d.timeType = d.timeType || 'datetime';
                    if (d.field !== false && d.search !== false) {
                        switch (d.search) {
                            case true:
                                formHtml += '\t<div class="layui-form-item layui-inline">\n' +
                                    '<label class="layui-form-label">' + d.title + '</label>\n' +
                                    '<div class="layui-input-inline">\n' +
                                    '<input id="c-' + d.fieldAlias + '" name="' + d.fieldAlias + '" data-search-op="' + d.searchOp + '" value="' + d.searchValue + '" placeholder="' + d.searchTip + '" class="layui-input">\n' +
                                    '</div>\n' +
                                    '</div>';
                                break;
                            case  'select':
                                d.searchOp = '=';
                                var selectHtml = '';
                                $.each(d.selectList, function (sI, sV) {
                                    var selected = '';
                                    if (sI === d.searchValue) {
                                        selected = 'selected=""';
                                    }
                                    selectHtml += '<option value="' + sI + '" ' + selected + '>' + sV + '</option>/n';
                                });
                                formHtml += '\t<div class="layui-form-item layui-inline">\n' +
                                    '<label class="layui-form-label">' + d.title + '</label>\n' +
                                    '<div class="layui-input-inline">\n' +
                                    '<select class="layui-select" id="c-' + d.fieldAlias + '" name="' + d.fieldAlias + '"  data-search-op="' + d.searchOp + '" >\n' +
                                    '<option value="">-'+ findInnerText('全部')+ '-</option> \n' +
                                    selectHtml +
                                    '</select>\n' +
                                    '</div>\n' +
                                    '</div>';
                                break;
                            case 'range':
                                d.searchOp = 'range';
                                formHtml += '\t<div class="layui-form-item layui-inline">\n' +
                                    '<label class="layui-form-label">' + d.title + '</label>\n' +
                                    '<div class="layui-input-inline">\n' +
                                    '<input id="c-' + d.fieldAlias + '" name="' + d.fieldAlias + '"  data-search-op="' + d.searchOp + '"  value="' + d.searchValue + '" placeholder="' + d.searchTip + '" class="layui-input">\n' +
                                    '</div>\n' +
                                    '</div>';
                                break;
                            case 'time':
                                d.searchOp = '=';
                                formHtml += '\t<div class="layui-form-item layui-inline">\n' +
                                    '<label class="layui-form-label">' + d.title + '</label>\n' +
                                    '<div class="layui-input-inline">\n' +
                                    '<input id="c-' + d.fieldAlias + '" name="' + d.fieldAlias + '"  data-search-op="' + d.searchOp + '"  value="' + d.searchValue + '" placeholder="' + d.searchTip + '" class="layui-input">\n' +
                                    '</div>\n' +
                                    '</div>';
                                break;
                        }
                        newCols.push(d);
                    }
                });
                if (formHtml !== '') {
                    // $(elem).before('<fieldset id="searchFieldset_' + tableId + '" class="table-search-fieldset layui-hide">\n'
                    $(elem).before('<fieldset id="searchFieldset_' + tableId + '" class="table-search-fieldset layui-hide">\n' +
                        '<legend>'+findInnerText('条件搜索')+'</legend>\n' +
                        '<form class="layui-form layui-form-pane form-search">\n' +
                        formHtml +
                        '<div class="layui-form-item layui-inline" style="margin-left: 115px">\n' +
                        '<button type="submit" class="layui-btn layui-btn-normal" data-type="tableSearch" data-table="' + tableId + '" lay-submit lay-filter="' + tableId + '_filter"> '+ findInnerText('搜 索')+'</button>\n' +
                        '<button type="reset" class="layui-btn layui-btn-primary" data-table-reset="' + tableId + '"> '+ findInnerText('重 置')+'</button>\n' +
                        ' </div>' +
                        '</form>' +
                        '</fieldset>');

                    admin.table.listenTableSearch(tableId);

                    // 初始化form表单
                    form.render();
                    $.each(newCols, function (ncI, ncV) {
                        if (ncV.search === 'range') {
                            laydate.render({range: true, type: ncV.timeType, elem: '[name="' + ncV.field + '"]'});
                        }
                        if (ncV.search === 'time') {
                            laydate.render({type: ncV.timeType, elem: '[name="' + ncV.field + '"]'});
                        }
                    });
                }
            },
            renderSwitch: function (cols, tableInit, tableId, modifyReload) {
                tableInit.modify_url = tableInit.modify_url || false;
                cols = cols[0] || {};
                tableId = tableId || init.table_render_id;
                if (cols.length > 0) {
                    $.each(cols, function (i, v) {
                        v.filter = v.filter || false;
                        if (v.filter !== false && tableInit.modify_url !== false) {
                            admin.table.listenSwitch({filter: v.filter, url: tableInit.modify_url, tableId: tableId, modifyReload: modifyReload});
                        }
                    });
                }
            },
            renderOperat(data, elem) {
                for (dk in data) {
                    var col = data[dk];
                    var operat = col[col.length - 1].operat;
                    if (operat !== undefined) {
                        var check = false;
                        for (key in operat) {
                            var item = operat[key];
                            if (typeof item === 'string') {
                                if (admin.checkAuth(item, elem)) {
                                    check = true;
                                    break;
                                }
                            } else {
                                for (k in item) {
                                    var v = item[k];
                                    if (v.auth !== undefined && admin.checkAuth(v.auth, elem)) {
                                        check = true;
                                        break;
                                    }
                                }
                            }
                        }
                        if (!check) {
                            data[dk].pop()
                        }
                    }
                }
                return data;
            },
            buildToolbarHtml: function (toolbar, tableId) {
                var html = '';
                toolbar.class = toolbar.class || '';
                toolbar.icon = toolbar.icon || '';
                toolbar.auth = toolbar.auth || '';
                toolbar.url = toolbar.url || '';
                toolbar.extend = toolbar.extend || '';
                toolbar.method = toolbar.method || 'open';
                toolbar.field = toolbar.field || 'id';
                toolbar.title = toolbar.title || toolbar.text;
                toolbar.text = toolbar.text || toolbar.title;
                toolbar.checkbox = toolbar.checkbox || false;

                var formatToolbar = toolbar;
                formatToolbar.icon = formatToolbar.icon !== '' ? '<i class="' + formatToolbar.icon + '"></i> ' : '';
                formatToolbar.class = formatToolbar.class !== '' ? 'class="' + formatToolbar.class + '" ' : '';
                if (toolbar.method === 'open') {
                    formatToolbar.method = formatToolbar.method !== '' ? 'data-open="' + formatToolbar.url + '" data-title="' + formatToolbar.title + '" ' : '';
                } else {
                    formatToolbar.method = formatToolbar.method !== '' ? 'data-request="' + formatToolbar.url + '" data-title="' + formatToolbar.title + '" ' : '';
                }
                formatToolbar.checkbox = toolbar.checkbox ? ' data-checkbox="true" ' : '';
                formatToolbar.tableId = tableId !== undefined ? ' data-table="' + tableId + '" ' : '';
                html = '<button ' + formatToolbar.class + formatToolbar.method + formatToolbar.extend + formatToolbar.checkbox +  formatToolbar.tableId + '>' + formatToolbar.icon + formatToolbar.text + '</button>';

                return html;
            },
            buildOperatHtml: function (operat) {
                var html = '';
                operat.class = operat.class || '';
                operat.icon = operat.icon || '';
                operat.auth = operat.auth || '';
                operat.url = operat.url || '';
                operat.extend = operat.extend || '';
                operat.method = operat.method || 'open';
                operat.field = operat.field || 'id';
                operat.title = operat.title || operat.text;
                operat.text = operat.text || operat.title;

                var formatOperat = operat;
                formatOperat.icon = formatOperat.icon !== '' ? '<i class="' + formatOperat.icon + '"></i> ' : '';
                formatOperat.class = formatOperat.class !== '' ? 'class="' + formatOperat.class + '" ' : '';
                if (operat.method === 'open') {
                    formatOperat.method = formatOperat.method !== '' ? 'data-open="' + formatOperat.url + '" data-title="' + formatOperat.title + '" ' : '';
                } else {
                    formatOperat.method = formatOperat.method !== '' ? 'data-request="' + formatOperat.url + '" data-title="' + formatOperat.title + '" ' : '';
                }
                html = '<a ' + formatOperat.class + formatOperat.method + formatOperat.extend + '>' + formatOperat.icon + formatOperat.text + '</a>';

                return html;
            },
            toolSpliceUrl(url, field, data) {
                url = url.indexOf("?") !== -1 ? url + '&' + field + '=' + data[field] : url + '?' + field + '=' + data[field];
                return url;
            },
            formatCols: function (cols, init) {
                for (i in cols) {
                    var col = cols[i];
                    for (index in col) {
                        var val = col[index];

                        // 判断是否包含初始化数据
                        if (val.init === undefined) {
                            cols[i][index]['init'] = init;
                        }

                        // 格式化列操作栏
                        if (val.templet === admin.table.tool && val.operat === undefined) {
                            cols[i][index]['operat'] = ['edit', 'delete'];
                        }

                        // 判断是否包含开关组件
                        if (val.templet === admin.table.switch && val.filter === undefined) {
                            cols[i][index]['filter'] = val.field;
                        }

                        // 判断是否含有搜索下拉列表
                        if (val.selectList !== undefined && val.search === undefined) {
                            cols[i][index]['search'] = 'select';
                        }

                        // 判断是否初始化对齐方式
                        if (val.align === undefined) {
                            cols[i][index]['align'] = 'center';
                        }

                        // 部分字段开启排序
                        var sortDefaultFields = ['id', 'sort'];
                        if (val.sort === undefined && sortDefaultFields.indexOf(val.field) >= 0) {
                            cols[i][index]['sort'] = true;
                        }

                        // 初始化图片高度
                        if (val.templet === admin.table.image && val.imageHeight === undefined) {
                            cols[i][index]['imageHeight'] = 40;
                        }

                        // 判断是否多层对象
                        if (val.field !== undefined && val.field.split(".").length > 1) {
                            if (val.templet === undefined) {
                                cols[i][index]['templet'] = admin.table.value;
                            }
                        }

                        // 判断是否列表数据转换
                        if (val.selectList !== undefined && val.templet === undefined) {
                            cols[i][index]['templet'] = admin.table.list;
                        }

                    }
                }
                return cols;
            },
            tool: function (data, option) {
                option.operat = option.operat || ['edit', 'delete'];
                var elem = option.init.table_elem || init.table_elem;
                var html = '';
                $.each(option.operat, function (i, item) {
                    if (typeof item === 'string') {
                        switch (item) {
                            case 'edit':
                                var operat = {
                                    class: 'layui-btn layui-btn-success layui-btn-xs',
                                    method: 'open',
                                    field: 'id',
                                    icon: '',
                                    text: findInnerText('编辑'),
                                    title: findInnerText('编辑信息'),
                                    auth: 'edit',
                                    url: option.init.edit_url,
                                    extend: ""
                                };
                                operat.url = admin.table.toolSpliceUrl(operat.url, operat.field, data);
                                if (admin.checkAuth(operat.auth, elem)) {
                                    html += admin.table.buildOperatHtml(operat);
                                }
                                break;
                            case 'delete':
                                var operat = {
                                    class: 'layui-btn layui-btn-danger layui-btn-xs',
                                    method: 'get',
                                    field: 'id',
                                    icon: '',
                                    text: findInnerText('删除'),
                                    title: findInnerText('确定删除')+'？',
                                    auth: 'delete',
                                    url: option.init.delete_url,
                                    extend: ""
                                };
                                operat.url = admin.table.toolSpliceUrl(operat.url, operat.field, data);
                                if (admin.checkAuth(operat.auth, elem)) {
                                    html += admin.table.buildOperatHtml(operat);
                                }
                                break;
                        }

                    } else if (typeof item === 'object') {
                        $.each(item, function (i, operat) {
                            operat.class = operat.class || '';
                            operat.icon = operat.icon || '';
                            operat.auth = operat.auth || '';
                            operat.url = operat.url || '';
                            operat.method = operat.method || 'open';
                            operat.field = operat.field || 'id';
                            operat.title = operat.title || operat.text;
                            operat.text = operat.text || operat.title;
                            operat.extend = operat.extend || '';
                            operat.url = admin.table.toolSpliceUrl(operat.url, operat.field, data);
                            if (admin.checkAuth(operat.auth, elem) && data[operat.auth]!==true) {
                                if (operat.huanhang===true) {
                                    html += admin.table.buildOperatHtml(operat)+"<br/>";
                                }else{
                                    html += admin.table.buildOperatHtml(operat);
                                }

                            }
                        });
                    }
                });
                return html;
            },
            list: function (data, option) {
                option.selectList = option.selectList || {};
                var field = option.field;
                try {
                    var value = eval("data." + field);
                } catch (e) {
                    var value = undefined;
                }
                if (option.selectList[value] === undefined || option.selectList[value] === '' || option.selectList[value] === null) {
                    return value;
                } else {
                    return option.selectList[value];
                }
            },
            image: function (data, option) {
                option.imageWidth = option.imageWidth || 200;
                option.imageHeight = option.imageHeight || 40;
                option.imageSplit = option.imageSplit || '|';
                option.imageJoin = option.imageJoin || '<br>';
                option.title = option.field;
                var field = option.field,
                    title = data[option.title];
                try {
                    var value = eval("data." + field);
                } catch (e) {
                    var value = undefined;
                }
                if (value === undefined || value===null) {
                    return '<img style="max-width: ' + option.imageWidth + 'px; max-height: ' + option.imageHeight + 'px;" src="' + value + '" data-image="' + title + '">';
                } else {
                    var values = value.split(option.imageSplit),
                        valuesHtml = [];
                    values.forEach((value, index) => {
                        valuesHtml.push('<img style="max-width: ' + option.imageWidth + 'px; max-height: ' + option.imageHeight + 'px;" src="' + value + '" data-image="' + title + '">');
                    });
                    return valuesHtml.join(option.imageJoin);
                }
            },
            url: function (data, option) {
                var field = option.field;
                try {
                    var value = eval("data." + field);
                } catch (e) {
                    var value = undefined;
                }
                return '<a class="layuimini-table-url" href="' + value + '" target="_blank" class="label bg-green">' + value + '</a>';
            },
            switch: function (data, option) {
                var field = option.field;
                option.filter = option.filter || option.field || null;
                option.checked = option.checked || 1;
                option.tips = option.tips || findInnerText('开|关');
                try {
                    var value = eval("data." + field);
                } catch (e) {
                    var value = undefined;
                }
                var checked = value === option.checked ? 'checked' : '';
                return laytpl('<input type="checkbox" name="' + option.field + '" value="' + data.id + '" lay-skin="switch" lay-text="' + option.tips + '" lay-filter="' + option.filter + '" ' + checked + ' >').render(data);
            },
            price: function (data, option) {
                var field = option.field;
                try {
                    var value = eval("data." + field);
                } catch (e) {
                    var value = undefined;
                }
                return '<span>￥' + value + '</span>';
            },
            percent: function (data, option) {
                var field = option.field;
                try {
                    var value = eval("data." + field);
                } catch (e) {
                    var value = undefined;
                }
                return '<span>' + value + '%</span>';
            },
            icon: function (data, option) {
                var field = option.field;
                try {
                    var value = eval("data." + field);
                } catch (e) {
                    var value = undefined;
                }
                return '<i class="' + value + '"></i>';
            },
            text: function (data, option) {
                var field = option.field;
                try {
                    var value = eval("data." + field);
                } catch (e) {
                    var value = undefined;
                }
                return '<span class="line-limit-length">' + value + '</span>';
            },
            value: function (data, option) {
                var field = option.field;
                try {
                    var value = eval("data." + field);
                } catch (e) {
                    var value = undefined;
                }
                return '<span>' + value + '</span>';
            },
            listenTableSearch: function (tableId) {
                form.on('submit(' + tableId + '_filter)', function (data) {
                    var dataField = data.field;
                    var formatFilter = {},
                        formatOp = {};
                    $.each(dataField, function (key, val) {
                        if (val !== '') {
                            formatFilter[key] = val;
                            var op = $('#c-' + key).attr('data-search-op');
                            op = op || '%*%';
                            formatOp[key] = op;
                        }
                    });
                    table.reload(tableId, {
                        page: {
                            curr: 1
                        }
                        , where: {
                            filter: JSON.stringify(formatFilter),
                            op: JSON.stringify(formatOp)
                        }
                    }, 'data');
                    return false;
                });
            },
            listenSwitch: function (option, ok) {
                option.filter = option.filter || '';
                option.url = option.url || '';
                option.field = option.field || option.filter || '';
                option.tableId = option.tableId || init.table_render_id;
                option.modifyReload = option.modifyReload || false;
                form.on('switch(' + option.filter + ')', function (obj) {
                    var checked = obj.elem.checked ? 1 : 0;
                    if (typeof ok === 'function') {
                        return ok({
                            id: obj.value,
                            checked: checked,
                        });
                    } else {
                        var data = {
                            id: obj.value,
                            field: option.field,
                            value: checked,
                        };
                        admin.request.post({
                            url: option.url,
                            prefix: true,
                            data: data,
                        }, function (res) {
                            if (option.modifyReload) {
                                table.reload(option.tableId);
                            }
                        }, function (res) {
                            admin.msg.error(res.msg, function () {
                                table.reload(option.tableId);
                            });
                        }, function () {
                            table.reload(option.tableId);
                        });
                    }
                });
            },
            listenToolbar: function (layFilter, tableId) {
                table.on('toolbar(' + layFilter + ')', function (obj) {

                    // 搜索表单的显示
                    switch (obj.event) {
                        case 'TABLE_SEARCH':
                            var searchFieldsetId = 'searchFieldset_' + tableId;
                            var _that = $("#" + searchFieldsetId);
                            if (_that.hasClass("layui-hide")) {
                                _that.removeClass('layui-hide');
                            } else {
                                _that.addClass('layui-hide');
                            }
                            break;
                    }
                });
            },
            listenEdit: function (tableInit, layFilter, tableId, modifyReload) {
                tableInit.modify_url = tableInit.modify_url || false;
                tableId = tableId || init.table_render_id;
                if (tableInit.modify_url !== false) {
                    table.on('edit(' + layFilter + ')', function (obj) {
                        var value = obj.value,
                            data = obj.data,
                            id = data.id,
                            field = obj.field;
                        var _data = {
                            id: id,
                            field: field,
                            value: value,
                        };
                        admin.request.post({
                            url: tableInit.modify_url,
                            prefix: true,
                            data: _data,
                        }, function (res) {
                            if (modifyReload) {
                                table.reload(tableId);
                            }
                        }, function (res) {
                            admin.msg.error(res.msg, function () {
                                table.reload(tableId);
                            });
                        }, function () {
                            table.reload(tableId);
                        });
                    });
                }
            },
        },
        checkMobile: function () {
            var userAgentInfo = navigator.userAgent;
            var mobileAgents = ["Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod"];
            var mobile_flag = false;
            //根据userAgent判断是否是手机
            for (var v = 0; v < mobileAgents.length; v++) {
                if (userAgentInfo.indexOf(mobileAgents[v]) > 0) {
                    mobile_flag = true;
                    break;
                }
            }
            var screen_width = window.screen.width;
            var screen_height = window.screen.height;
            //根据屏幕分辨率判断是否是手机
            if (screen_width < 600 && screen_height < 800) {
                mobile_flag = true;
            }
            return mobile_flag;
        },
        open: function (title, url, width, height, isResize) {
            isResize = isResize === undefined ? true : isResize;
            var index = layer.open({
                title: title,
                type: 2,
                area: [width, height],
                content: url,
                maxmin: true,
                moveOut: true,
                success: function (layero, index) {
                    var body = layer.getChildFrame('body', index);
                    if (body.length > 0) {
                        $.each(body, function (i, v) {

                            // todo 优化弹出层背景色修改
                            $(v).before('<style>\n' +
                                'html, body {\n' +
                                '    background: #ffffff;\n' +
                                '}\n' +
                                '</style>');
                        });
                    }
                }
            });
            if (admin.checkMobile() || width === undefined || height === undefined) {
                layer.full(index);
            }
            if (isResize) {
                $(window).on("resize", function () {
                    layer.full(index);
                })
            }
        },
        listen: function (preposeCallback, ok, no, ex) {

            // 监听表单是否为必填项
            admin.api.formRequired();

            // 监听表单提交事件
            admin.api.formSubmit(preposeCallback, ok, no, ex);

            // 初始化图片显示以及监听上传事件
            admin.api.upload();

            // 监听富文本初始化
            admin.api.editor();

            // 监听下拉选择生成
            admin.api.select();

            // 监听时间控件生成
            admin.api.date();

            // 初始化layui表单
            form.render();

            // 表格修改
            $("body").on("mouseenter", ".table-edit-tips", function () {
                var openTips = layer.tips(findInnerText('点击行内容可以进行修改'), $(this), {tips: [2, '#e74c3c'], time: 4000});
            });

            // 监听弹出层的打开
            $('body').on('click', '[data-open]', function () {

                var clienWidth = $(this).attr('data-width'),
                    clientHeight = $(this).attr('data-height'),
                    dataFull = $(this).attr('data-full'),
                    checkbox = $(this).attr('data-checkbox'),
                    url = $(this).attr('data-open'),
                    tableId = $(this).attr('data-table');

                if(checkbox === 'true'){
                    tableId = tableId || init.table_render_id;
                    var checkStatus = table.checkStatus(tableId),
                        data = checkStatus.data;
                    if (data.length <= 0) {
                        admin.msg.error(findInnerText('请勾选需要操作的数据'));
                        return false;
                    }
                    var ids = [];
                    $.each(data, function (i, v) {
                        ids.push(v.id);
                    });
                    if (url.indexOf("?") === -1) {
                        url += '?id=' + ids.join(',');
                    } else {
                        url += '&id=' + ids.join(',');
                    }
                }

                if (clienWidth === undefined || clientHeight === undefined) {
                    var width = document.body.clientWidth,
                        height = document.body.clientHeight;
                    if (width >= 800 && height >= 600) {
                        clienWidth = '800px';
                        clientHeight = '600px';
                    } else {
                        clienWidth = '100%';
                        clientHeight = '100%';
                    }
                }
                if (dataFull === 'true') {
                    clienWidth = '100%';
                    clientHeight = '100%';
                }

                admin.open(
                    $(this).attr('data-title'),
                    admin.url(url),
                    clienWidth,
                    clientHeight,
                );
            });

            // 放大图片
            $('body').on('click', '[data-image]', function () {
                var title = $(this).attr('data-image'),
                    src = $(this).attr('src'),
                    alt = $(this).attr('alt');
                var photos = {
                    "title": title,
                    "id": Math.random(),
                    "data": [
                        {
                            "alt": alt,
                            "pid": Math.random(),
                            "src": src,
                            "thumb": src
                        }
                    ]
                };
                layer.photos({
                    photos: photos,
                    anim: 5
                });
                return false;
            });

            // 放大一组图片
            $('body').on('click', '[data-images]', function () {
                var title = $(this).attr('data-images'),
                    // 从当前元素向上找layuimini-upload-show找到第一个后停止, 再找其所有子元素li
                    doms = $(this).closest(".layuimini-upload-show").children("li"),
                    // 被点击的图片地址
                    now_src = $(this).attr('src'),
                    alt = $(this).attr('alt'),
                    data = [];
                $.each(doms, function(key, value){
                    var src = $(value).find('img').attr('src');
                    if(src != now_src){
                        // 压入其他图片地址
                        data.push({
                            "alt": alt,
                            "pid": Math.random(),
                            "src": src,
                            "thumb": src
                        });
                    }else{
                        // 把当前图片插入到头部
                        data.unshift({
                            "alt": alt,
                            "pid": Math.random(),
                            "src": now_src,
                            "thumb": now_src
                        });
                    }
                });
                var photos = {
                    "title": title,
                    "id": Math.random(),
                    "data": data,
                };
                layer.photos({
                    photos: photos,
                    anim: 5
                });
                return false;
            });


            // 监听动态表格刷新
            $('body').on('click', '[data-table-refresh]', function () {
                var tableId = $(this).attr('data-table-refresh');
                if (tableId === undefined || tableId === '' || tableId == null) {
                    tableId = init.table_render_id;
                }
                table.reload(tableId);
            });

            // 监听搜索表格重置
            $('body').on('click', '[data-table-reset]', function () {
                var tableId = $(this).attr('data-table-reset');
                if (tableId === undefined || tableId === '' || tableId == null) {
                    tableId = init.table_render_id;
                }
                table.reload(tableId, {
                    page: {
                        curr: 1
                    }
                    , where: {
                        filter: '{}',
                        op: '{}'
                    }
                }, 'data');
            });

            // 监听请求
            $('body').on('click', '[data-request]', function () {
                var title = $(this).attr('data-title'),
                    url = $(this).attr('data-request'),
                    tableId = $(this).attr('data-table'),
                    addons = $(this).attr('data-addons'),
                    checkbox = $(this).attr('data-checkbox'),
                    direct = $(this).attr('data-direct'),
                    field = $(this).attr('data-field') || 'id';

                title = title || findInnerText('确定进行该操作')+'？';

                if (direct === 'true') {
                    admin.msg.confirm(title, function () {
                        window.location.href = url;
                    });
                    return false;
                }

                var postData = {};
                if(checkbox === 'true'){
                    tableId = tableId || init.table_render_id;
                    var checkStatus = table.checkStatus(tableId),
                        data = checkStatus.data;
                    if (data.length <= 0) {
                        admin.msg.error(findInnerText('请勾选需要操作的数据'));
                        return false;
                    }
                    var ids = [];
                    $.each(data, function (i, v) {
                        ids.push(v[field]);
                    });
                    postData[field] = ids;
                }

                if (addons !== true && addons !== 'true') {
                    url = admin.url(url);
                }
                tableId = tableId || init.table_render_id;
                admin.msg.confirm(title, function () {
                    admin.request.post({
                        url: url,
                        data: postData,
                    }, function (res) {
                        admin.msg.success(res.msg, function () {
                            table.reload(tableId);
                        });
                    })
                });
                return false;
            });


            // 数据表格多删除
            $('body').on('click', '[data-table-delete]', function () {
                var tableId = $(this).attr('data-table-delete'),
                    url = $(this).attr('data-url');
                tableId = tableId || init.table_render_id;
                url = url !== undefined ? admin.url(url) : window.location.href;
                var checkStatus = table.checkStatus(tableId),
                    data = checkStatus.data;
                if (data.length <= 0) {
                    admin.msg.error(findInnerText('请勾选需要删除的数据'));
                    return false;
                }
                var ids = [];
                $.each(data, function (i, v) {
                    ids.push(v.id);
                });
                admin.msg.confirm(findInnerText('确定删除')+'？', function () {
                    admin.request.post({
                        url: url,
                        data: {
                            id: ids
                        },
                    }, function (res) {
                        admin.msg.success(res.msg, function () {
                            table.reload(tableId);
                        });
                    });
                });
                return false;
            });

        },
        api: {
            form: function (url, data, ok, no, ex, refreshTable) {
                if (refreshTable === undefined) {
                    refreshTable = true;
                }
                ok = ok || function (res) {
                    res.msg = res.msg || '';
                    admin.msg.success(res.msg, function () {
                        admin.api.closeCurrentOpen({
                            refreshTable: refreshTable
                        });
                    });
                    return false;
                };
                admin.request.post({
                    url: url,
                    data: data,
                }, ok, no, ex);
                return false;
            },
            closeCurrentOpen: function (option) {
                option = option || {};
                option.refreshTable = option.refreshTable || false;
                option.refreshFrame = option.refreshFrame || false;
                if (option.refreshTable === true) {
                    option.refreshTable = init.table_render_id;
                }
                var index = parent.layer.getFrameIndex(window.name);
                parent.layer.close(index);
                if (option.refreshTable !== false) {
                    parent.layui.table.reload(option.refreshTable);
                }
                if (option.refreshFrame) {
                    parent.location.reload();
                }
                return false;
            },
            refreshFrame: function () {
                parent.location.reload();
                return false;
            },
            refreshTable: function (tableName) {
                tableName = tableName || 'currentTable';
                table.reload(tableName);
            },
            formRequired: function () {
                var verifyList = document.querySelectorAll("[lay-verify]");
                if (verifyList.length > 0) {
                    $.each(verifyList, function (i, v) {
                        var verify = $(this).attr('lay-verify');

                        // todo 必填项处理
                        if (verify === 'required') {
                            var label = $(this).parent().prev();
                            if (label.is('label') && !label.hasClass('required')) {
                                label.addClass('required');
                            }
                            if ($(this).attr('lay-reqtext') === undefined && $(this).attr('placeholder') !== undefined) {
                                $(this).attr('lay-reqtext', $(this).attr('placeholder'));
                            }
                            if ($(this).attr('placeholder') === undefined && $(this).attr('lay-reqtext') !== undefined) {
                                $(this).attr('placeholder', $(this).attr('lay-reqtext'));
                            }
                        }

                    });
                }
            },
            formSubmit: function (preposeCallback, ok, no, ex) {
                var formList = document.querySelectorAll("[lay-submit]");

                // 表单提交自动处理
                if (formList.length > 0) {
                    $.each(formList, function (i, v) {
                        var filter = $(this).attr('lay-filter'),
                            type = $(this).attr('data-type'),
                            refresh = $(this).attr('data-refresh'),
                            url = $(this).attr('lay-submit');
                        // 表格搜索不做自动提交
                        if (type === 'tableSearch') {
                            return false;
                        }
                        // 判断是否需要刷新表格
                        if (refresh === 'false') {
                            refresh = false;
                        } else {
                            refresh = true;
                        }
                        // 自动添加layui事件过滤器
                        if (filter === undefined || filter === '') {
                            filter = 'save_form_' + (i + 1);
                            $(this).attr('lay-filter', filter)
                        }
                        if (url === undefined || url === '' || url === null) {
                            url = window.location.href;
                        } else {
                            url = admin.url(url);
                        }
                        form.on('submit(' + filter + ')', function (data) {
                            var dataField = data.field;

                            // 富文本数据处理
                            var editorList = document.querySelectorAll(".editor");
                            if (editorList.length > 0) {
                                $.each(editorList, function (i, v) {
                                    var name = $(this).attr("name");
                                    dataField[name] = CKEDITOR.instances[name].getData();
                                });
                            }

                            if (typeof preposeCallback === 'function') {
                                dataField = preposeCallback(dataField);
                            }
                            admin.api.form(url, dataField, ok, no, ex, refresh);

                            return false;
                        });
                    });
                }

            },
            upload: function () {
                var uploadList = document.querySelectorAll("[data-upload]");
                var uploadSelectList = document.querySelectorAll("[data-upload-select]");

                if (uploadList.length > 0) {
                    $.each(uploadList, function (i, v) {
                        var exts = $(this).attr('data-upload-exts'),
                            uploadName = $(this).attr('data-upload'),
                            uploadNumber = $(this).attr('data-upload-number'),
                            uploadSign = $(this).attr('data-upload-sign');
                        exts = exts || init.upload_exts;
                        uploadNumber = uploadNumber || 'one';
                        uploadSign = uploadSign || '|';
                        var elem = "input[name='" + uploadName + "']",
                            uploadElem = this;

                        // 监听上传事件
                        upload.render({
                            elem: this,
                            url: admin.url(init.upload_url),
                            accept: 'file',
                            exts: exts,
                            // 让多图上传模式下支持多选操作
                            multiple: (uploadNumber !== 'one') ? true : false,
                            done: function (res) {
                                if (res.code === 1) {
                                    var url = res.data.url;
                                    if (uploadNumber !== 'one') {
                                        var oldUrl = $(elem).val();
                                        if (oldUrl !== '') {
                                            url = oldUrl + uploadSign + url;
                                        }
                                    }
                                    $(elem).val(url);
                                    $(elem).trigger("input");
                                    admin.msg.success(res.msg);
                                } else {
                                    admin.msg.error(res.msg);
                                }
                                return false;
                            }
                        });

                        // 监听上传input值变化
                        $(elem).bind("input propertychange", function (event) {
                            var urlString = $(this).val(),
                                urlArray = urlString.split(uploadSign),
                                uploadIcon = $(uploadElem).attr('data-upload-icon');
                            uploadIcon = uploadIcon || "file";

                            $('#bing-' + uploadName).remove();
                            if (urlString.length > 0) {
                                var parant = $(this).parent('div');
                                var liHtml = '';
                                $.each(urlArray, function (i, v) {
                                    liHtml += '<li><a><img src="' + v + '" data-image  onerror="this.src=\'' + BASE_URL + 'admin/images/upload-icons/' + uploadIcon + '.png\';this.onerror=null"></a><small class="uploads-delete-tip bg-red badge" data-upload-delete="' + uploadName + '" data-upload-url="' + v + '" data-upload-sign="' + uploadSign + '">×</small></li>\n';
                                });
                                parant.after('<ul id="bing-' + uploadName + '" class="layui-input-block layuimini-upload-show">\n' + liHtml + '</ul>');
                            }

                        });

                        // 非空初始化图片显示
                        if ($(elem).val() !== '') {
                            $(elem).trigger("input");
                        }
                    });

                    // 监听上传文件的删除事件
                    $('body').on('click', '[data-upload-delete]', function () {
                        var uploadName = $(this).attr('data-upload-delete'),
                            deleteUrl = $(this).attr('data-upload-url'),
                            sign = $(this).attr('data-upload-sign');
                        var confirm = admin.msg.confirm(findInnerText('确定删除')+'？', function () {
                            var elem = "input[name='" + uploadName + "']";
                            var currentUrl = $(elem).val();
                            var url = '';
                            if (currentUrl !== deleteUrl) {
                                url = currentUrl.search(deleteUrl) === 0 ? currentUrl.replace(deleteUrl + sign, '') : currentUrl.replace(sign + deleteUrl, '');
                                $(elem).val(url);
                                $(elem).trigger("input");
                            } else {
                                $(elem).val(url);
                                $('#bing-' + uploadName).remove();
                            }
                            admin.msg.close(confirm);
                        });
                        return false;
                    });
                }

                if (uploadSelectList.length > 0) {
                    $.each(uploadSelectList, function (i, v) {
                        var exts = $(this).attr('data-upload-exts'),
                            uploadName = $(this).attr('data-upload-select'),
                            uploadNumber = $(this).attr('data-upload-number'),
                            uploadSign = $(this).attr('data-upload-sign');
                        exts = exts || init.upload_exts;
                        uploadNumber = uploadNumber || 'one';
                        uploadSign = uploadSign || '|';
                        var selectCheck = uploadNumber === 'one' ? 'radio' : 'checkbox';
                        var elem = "input[name='" + uploadName + "']",
                            uploadElem = $(this).attr('id');

                        tableSelect.render({
                            elem: "#" + uploadElem,
                            checkedKey: 'id',
                            searchType: 'more',
                            searchList: [
                                {searchKey: 'title', searchPlaceholder: findInnerText('请输入文件名')},
                            ],
                            table: {
                                url: admin.url('ajax/getUploadFiles'),
                                cols: [[
                                    {type: selectCheck},
                                    {field: 'id', title: 'ID'},
                                    {field: 'url', minWidth: 80, search: false, title: findInnerText('图片信息'), imageHeight: 40, align: "center", templet: admin.table.image},
                                    {field: 'original_name', width: 150, title: findInnerText('文件原名'), align: "center"},
                                    {field: 'mime_type', width: 120, title: findInnerText('mime类型'), align: "center"},
                                    {field: 'create_time', width: 200, title: findInnerText('创建时间'), align: "center", search: 'range'},
                                ]]
                            },
                            done: function (e, data) {
                                var urlArray = [];
                                $.each(data.data, function (index, val) {
                                    urlArray.push(val.url)
                                });
                                var url = urlArray.join(uploadSign);
                                admin.msg.success(findInnerText('选择成功'), function () {
                                    $(elem).val(url);
                                    $(elem).trigger("input");
                                });
                            }
                        })

                    });

                }
            },
            editor: function () {
                var editorList = document.querySelectorAll(".editor");
                if (editorList.length > 0) {
                    $.each(editorList, function (i, v) {
                        CKEDITOR.replace(
                            $(this).attr("name"),
                            {
                                height: $(this).height(),
                                filebrowserImageUploadUrl: admin.url('ajax/uploadEditor'),
                            });
                    });
                }
            },
            select: function () {
                var selectList = document.querySelectorAll("[data-select]");
                $.each(selectList, function (i, v) {
                    var url = $(this).attr('data-select'),
                        selectFields = $(this).attr('data-fields'),
                        value = $(this).attr('data-value'),
                        that = this,
                        html = '<option value=""></option>';
                    var fields = selectFields.replace(/\s/g, "").split(',');
                    if (fields.length !== 2) {
                        return admin.msg.error(findInnerText('下拉选择字段有误'));
                    }
                    admin.request.get(
                        {
                            url: url,
                            data: {
                                selectFields: selectFields
                            },
                        }, function (res) {
                            var list = res.data;
                            list.forEach(val => {
                                var key = val[fields[0]];
                                if (value !== undefined && key.toString() === value) {
                                    html += '<option value="' + key + '" selected="">' + val[fields[1]] + '</option>';
                                } else {
                                    html += '<option value="' + key + '">' + val[fields[1]] + '</option>';
                                }
                            });
                            $(that).html(html);
                            form.render();
                        }
                    );
                });
            },
            date: function () {
                var dateList = document.querySelectorAll("[data-date]");
                if (dateList.length > 0) {
                    $.each(dateList, function (i, v) {
                        var format = $(this).attr('data-date'),
                            type = $(this).attr('data-date-type'),
                            range = $(this).attr('data-date-range');
                        if(type === undefined || type === '' || type ===null){
                            type = 'datetime';
                        }
                        var options = {
                            elem: this,
                            type: type,
                        };
                        if (format !== undefined && format !== '' && format !== null) {
                            options['format'] = format;
                        }
                        if (range !== undefined) {
                            if(range === null || range === ''){
                                range = '-';
                            }
                            options['range'] = range;
                        }
                        laydate.render(options);
                    });
                }
            },
        },
    };
    return admin;
});
