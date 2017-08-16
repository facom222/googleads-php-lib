<?php
/**
 * Copyright 2016 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\AdsApi\Examples\AdWords\v201609\Remarketing;

require __DIR__ . '/../../../../vendor/autoload.php';

use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201609\cm\OfflineConversionFeed;
use Google\AdsApi\AdWords\v201609\cm\OfflineConversionFeedOperation;
use Google\AdsApi\AdWords\v201609\cm\OfflineConversionFeedService;
use Google\AdsApi\AdWords\v201609\cm\Operator;
use Google\AdsApi\Common\OAuth2TokenBuilder;

/**
 * This code example imports offline conversion values for specific clicks to
 * your account. To get Google Click ID for a click, run
 * CLICK_PERFORMANCE_REPORT.
 */
class UploadOfflineConversions {

  const CONVERSION_NAME = 'INSERT_CONVERSION_NAME_HERE';
  const GCLID = 'INSERT_GOOGLE_CLICK_ID_HERE';
  const CONVERSION_TIME = 'INSERT_CONVERSION_TIME_HERE';
  const CONVERSION_VALUE = 'INSERT_CONVERSION_VALUE_HERE';

  public static function runExample(
      AdWordsServices $adWordsServices,
      AdWordsSession $session,
      $conversionName,
      $gclid,
      $conversionTime,
      $conversionValue
  ) {
    $offlineConversionService =
        $adWordsServices->get($session, OfflineConversionFeedService::class);

    // Associate offline conversions with the existing named conversion tracker.
    // If this tracker was newly created, it may be a few hours before it can
    // accept conversions.
    $feed = new OfflineConversionFeed();
    $feed->setConversionName($conversionName);
    $feed->setConversionTime($conversionTime);
    $feed->setConversionValue($conversionValue);
    $feed->setGoogleClickId($gclid);

    $offlineConversionOperation = new OfflineConversionFeedOperation();
    $offlineConversionOperation->setOperator(Operator::ADD);
    $offlineConversionOperation->setOperand($feed);
    $offlineConversionOperations = [$offlineConversionOperation];

    $result = $offlineConversionService->mutate($offlineConversionOperations);

    $feed = $result->getValue()[0];
    printf(
        "Uploaded offline conversion value of %d for Google Click ID = "
            . "'%s' to '%s'.\n",
        $feed->getConversionValue(),
        $feed->getGoogleClickId(),
        $feed->getConversionName()
    );
  }

  public static function main() {
    // Generate a refreshable OAuth2 credential for authentication.
    $oAuth2Credential = (new OAuth2TokenBuilder())
        ->fromFile()
        ->build();

    // Construct an API session configured from a properties file and the OAuth2
    // credentials above.
    $session = (new AdWordsSessionBuilder())
        ->fromFile()
        ->withOAuth2Credential($oAuth2Credential)
        ->build();
    self::runExample(
        new AdWordsServices(),
        $session,
        self::CONVERSION_NAME,
        self::GCLID,
        self::CONVERSION_TIME,
        floatval(self::CONVERSION_VALUE)
    );
  }
}

UploadOfflineConversions::main();