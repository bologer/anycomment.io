<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

class Google_Service_DLP_GooglePrivacyDlpV2beta2Location extends Google_Collection
{
  protected $collection_key = 'imageBoxes';
  protected $byteRangeType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2Range';
  protected $byteRangeDataType = '';
  protected $codepointRangeType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2Range';
  protected $codepointRangeDataType = '';
  protected $fieldIdType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2FieldId';
  protected $fieldIdDataType = '';
  protected $imageBoxesType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2ImageLocation';
  protected $imageBoxesDataType = 'array';
  protected $recordKeyType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2RecordKey';
  protected $recordKeyDataType = '';
  protected $tableLocationType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2TableLocation';
  protected $tableLocationDataType = '';

  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2Range
   */
  public function setByteRange(Google_Service_DLP_GooglePrivacyDlpV2beta2Range $byteRange)
  {
    $this->byteRange = $byteRange;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2Range
   */
  public function getByteRange()
  {
    return $this->byteRange;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2Range
   */
  public function setCodepointRange(Google_Service_DLP_GooglePrivacyDlpV2beta2Range $codepointRange)
  {
    $this->codepointRange = $codepointRange;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2Range
   */
  public function getCodepointRange()
  {
    return $this->codepointRange;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2FieldId
   */
  public function setFieldId(Google_Service_DLP_GooglePrivacyDlpV2beta2FieldId $fieldId)
  {
    $this->fieldId = $fieldId;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2FieldId
   */
  public function getFieldId()
  {
    return $this->fieldId;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2ImageLocation
   */
  public function setImageBoxes($imageBoxes)
  {
    $this->imageBoxes = $imageBoxes;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2ImageLocation
   */
  public function getImageBoxes()
  {
    return $this->imageBoxes;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2RecordKey
   */
  public function setRecordKey(Google_Service_DLP_GooglePrivacyDlpV2beta2RecordKey $recordKey)
  {
    $this->recordKey = $recordKey;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2RecordKey
   */
  public function getRecordKey()
  {
    return $this->recordKey;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2TableLocation
   */
  public function setTableLocation(Google_Service_DLP_GooglePrivacyDlpV2beta2TableLocation $tableLocation)
  {
    $this->tableLocation = $tableLocation;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2TableLocation
   */
  public function getTableLocation()
  {
    return $this->tableLocation;
  }
}
