<?php

namespace Tests\Unit\Services;

use App\Services\Dcm4cheeService;
use Tests\TestCase;

class Dcm4cheeServiceTest extends TestCase
{
    public function test_genera_url_de_visor_correctamente(): void
    {
        $service = new Dcm4cheeService();
        $uid = '1.2.840.113619.2.55.3.604688419.864.1234567890';

        $url = $service->obtenerUrlVisor($uid);
        $this->assertStringContainsString('dcm4chee-arc/ui2/#/study/', $url);
        $this->assertStringContainsString($uid, $url);
    }

    public function test_genera_url_de_ohif_viewer_correctamente(): void
    {
        $service = new Dcm4cheeService();
        $uid = '1.2.840.113619.2.55.3.604688419.864.1234567890';

        $url = $service->obtenerUrlOhif($uid);
        $this->assertStringContainsString('StudyInstanceUIDs=', $url);
        $this->assertStringContainsString($uid, $url);
    }

    public function test_genera_url_wado_rendered_correctamente(): void
    {
        $service = new Dcm4cheeService();
        $studyUID = '1.2.3';
        $seriesUID = '4.5.6';
        $sopUID = '7.8.9';

        $url = $service->obtenerUrlWadoRendered($studyUID, $seriesUID, $sopUID);
        $this->assertStringContainsString("/studies/{$studyUID}/series/{$seriesUID}/instances/{$sopUID}/frames/1/rendered", $url);
    }
}
