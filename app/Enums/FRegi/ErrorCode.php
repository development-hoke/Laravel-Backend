<?php

namespace App\Enums\FRegi;

use App\Enums\BaseEnum;

/**
 * @see https://ssl.f-regi.com/fregitest/sa/misc/error_list?s=fehZX31vJ93UEivlHfpY
 */
final class ErrorCode extends BaseEnum
{
    // 識別できなかった場合のコード
    const Unrecognized = '99999';

    // オーソリ
    const EmptyCardNumber = 'A1-1-30';
    const InvalidCardNumber = 'A1-1-31';
    const EmptyExpiryMonth = 'A1-1-40';
    const InvalidExpiryMonth = 'A1-1-41';
    const EmptyExpiryYear = 'A1-1-50';
    const InvalidExpiryYear = 'A1-1-51';
    const Expired = 'A1-1-52';
    const FailedToIdentifyCardType = 'A1-1-81';
    const FailedToAuthorizeCard = 'A1-1-82';
    const InvalidCardTypeDebit = 'A1-1-83';
    const InvalidCardTypePrepaid = 'A1-1-84';
    const NotAllowedToUseForeignCard = 'A1-1-85';
    const EmptySecurityCode = 'A1-1-130';
    const TooLongSecurityCode = 'A1-1-131';
    const InvalidSecurityCode = 'A1-1-132';
    const InvalidToken = 'A1-1-170';
    const ExpiredToken = 'A1-1-171'; // トークン有効期限切れ

    // 承認金額変更
    const ChagneAuthTooManyAmount = 'A3-1-21';
    const ChagneAuthTooSmallAmount = 'A3-1-22';

    // 売上
    const SaleAuthExpired = 'S1-1-125'; // 売上処理時の指定された取引は売上処理期限を超過しています

    // 売上金額変更
    const ChangeSaleTooManyAmount = 'S3-1-21';
    const ChangeSaleTooSmallAmount = 'S3-1-22';

    // 共通
    const InvalidCard1 = 'JCN-G05';
    const InvalidCard2 = 'JCN-G12';
    const Pending = 'JCN-G30';
    const InvalidPinCode = 'JCN-G42';
    const InvalidSecurityCode2 = 'JCN-G44';
    const EmptySecurityCode2 = 'JCN-G45';
    const ExceedUsageCountLimit = 'JCN-G54';
    const ExceedUsageAmountLimit = 'JCN-G55';
    const InvalidCard3 = 'JCN-G56';
    const Accident = 'JCN-G60';
    const InvalidCard4 = 'JCN-G61';
    const InvalidMemberNumber = 'JCN-G65';
    const InvalidItemCode = 'JCN-G67';
    const InvalidPriceAmount = 'JCN-G68';
    const InvalidTaxOrOthers = 'JCN-G69';
    const InvalidBonusCount = 'JCN-G70';
    const InvalidBonusMonth = 'JCN-G71';
    const InvalidBonusAmount = 'JCN-G72';
    const InvalidStartMonth = 'JCN-G73';
    const InvalidSplitCount = 'JCN-G74';
    const InvalidSplitPrice = 'JCN-G75';
    const InvalidFirstPrice = 'JCN-G76';
    const InvalidBusinessSegument = 'JCN-G77';
    const InvalidPaymentMethod = 'JCN-G78';
    const InvalidCancelSegument = 'JCN-G80';
    const InvalidTreatmentSegument = 'JCN-G81';
    const InvalidExpiry = 'JCN-G83';
    const InvalidAuthorizationNumber = 'JCN-G84';
    const InvalidCAFISAgent = 'JCN-G85';
    const InvalidCardCompany = 'JCN-G92';
    const CyclicSerialNumbering = 'JCN-G94';
    const CardCompanyNotAvailable = 'JCN-G95';
    const Denied = 'JCN-G97';
    const InvalidBusinessTarget = 'JCN-G98';
    const DeniedConnection = 'JCN-G99';
}
