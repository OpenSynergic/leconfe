<?php

namespace Tests\Feature;

use App\Models\Proceeding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ProceedingDetailRouteTest extends TestCase
{
    public function test_proceeding_detail_route_rejects_non_numeric_parameters(): void
    {
        $route = Route::getRoutes()->getByName('livewirePageGroup.conference.pages.proceeding-detail');

        $this->assertSame('[0-9]+', $route->wheres['proceeding']);
        $this->assertFalse($route->matches(Request::create('/isoph/proceedings/view/wmlMYW9iAErq1D5eRO0LoOCR7g8odUbw2PYP7d26.png'), true));

        $this->assertNull(
            (new Proceeding)->resolveRouteBinding('wmlMYW9iAErq1D5eRO0LoOCR7g8odUbw2PYP7d26.png')
        );
    }
}
