<?php

namespace Fearless\Tool\Support;

use BenSampo\Enum\Enum;

final class ResponseCodeEnum extends Enum
{
    public const OK = 200;//操作成功
    public const CREATED = 201;//创建成功
    public const BAD_REQUEST = 400;//请求异常
    public const UNAUTHORIZED = 401;//没有进行认证或者认证非法
    public const FORBIDDEN = 403;//服务器已经理解请求，但是拒绝执行它
    public const NOT_FOUND = 404;//请求一个不存在的资源
    public const METHOD_NOT_ALLOWED = 405;//所请求的 HTTP 方法不允许当前认证用户访问
    public const UNPROCESSABLE_ENTITY = 422;// 用来表示校验错误
    public const HTTP_TOO_MANY_REQUESTS = 429;//请求太频繁
    public const SERVER_ERROR = 500;//服务器错误

    // 业务操作正确码：1xx、2xx、3xx 开头，后拼接 3 位
    // 200 + 001 => 200001，也就是有 001 ~ 999 个编号可以用来表示业务成功的情况，当然你可以根据实际需求继续增加位数，但必须要求是 200 开头
    // 举个栗子：你可以定义 001 ~ 099 表示系统状态；100 ~ 199 表示授权业务；200 ~ 299 表示用户业务...
    public const QUERY_SUCCESS = 200100;//查询成功
    public const UPDATE_SUCCESS = 200101;//更新成功
    public const DELETE_SUCCESS = 200102;//删除成功
    public const LOGIN_SUCCESS = 200103;//登录成功
    public const AUDIT_SUCCESS = 200104;//审核成功
    public const Error_STATUS = 422100;//状态不正确

    public const SQL_ERROR = 40001;//sql错误


    public static function getDescription($value): string
    {
        return trans("responseCode." . $value);
    }
}
