<?php
namespace Shopex\LubanSite\Middleware;

use Closure;
use Shopex\LubanSite\Facades\Site;

class WidgetsResource
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if($response->status()==200){
        	$response->setContent(Site::widgetsResource($response->content()));
        }
        return $response;
    }
}