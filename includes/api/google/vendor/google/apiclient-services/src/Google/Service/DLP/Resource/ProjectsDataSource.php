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

/**
 * The "dataSource" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dlpService = new Google_Service_DLP(...);
 *   $dataSource = $dlpService->dataSource;
 *  </code>
 */
class Google_Service_DLP_Resource_ProjectsDataSource extends Google_Service_Resource
{
  /**
   * Schedules a job to compute risk analysis metrics over content in a Google
   * Cloud Platform repository. [How-to guide}(/dlp/docs/compute-risk-analysis)
   * (dataSource.analyze)
   *
   * @param string $parent The parent resource name, for example projects/my-
   * project-id.
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2AnalyzeDataSourceRiskRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2DlpJob
   */
  public function analyze($parent, Google_Service_DLP_GooglePrivacyDlpV2beta2AnalyzeDataSourceRiskRequest $postBody, $optParams = array())
  {
    $params = array('parent' => $parent, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('analyze', array($params), "Google_Service_DLP_GooglePrivacyDlpV2beta2DlpJob");
  }
  /**
   * Schedules a job scanning content in a Google Cloud Platform data repository.
   * [How-to guide](/dlp/docs/inspecting-storage) (dataSource.inspect)
   *
   * @param string $parent The parent resource name, for example projects/my-
   * project-id.
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2InspectDataSourceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2DlpJob
   */
  public function inspect($parent, Google_Service_DLP_GooglePrivacyDlpV2beta2InspectDataSourceRequest $postBody, $optParams = array())
  {
    $params = array('parent' => $parent, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('inspect', array($params), "Google_Service_DLP_GooglePrivacyDlpV2beta2DlpJob");
  }
}
