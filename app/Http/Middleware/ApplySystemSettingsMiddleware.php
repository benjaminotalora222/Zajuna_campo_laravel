<?php

namespace App\Http\Middleware;

use App\Models\ConfiguracionSistema;
use App\Services\TranslationService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApplySystemSettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        // 1. Obtener idioma de sesión o de la base de datos
        $locale = session('locale');
        if (!$locale) {
            try {
                $locale = ConfiguracionSistema::get('idioma', 'es');
            } catch (\Throwable $e) {
                $locale = 'es';
            }
            session(['locale' => $locale]);
        }

        // 2. Aplicar zona horaria si está configurada
        try {
            $tz = ConfiguracionSistema::get('zona_horaria');
            if ($tz) {
                config(['app.timezone' => $tz]);
                date_default_timezone_set($tz);
            }
        } catch (\Throwable $e) {}

        // 3. Aplicar locale a Laravel y Carbon
        app()->setLocale($locale);
        Carbon::setLocale($locale);
        config(['app.locale' => $locale]);

        // 4. Compartir locale con las vistas
        view()->share('currentLocale', $locale);

        // 5. Continuar con la petición
        $response = $next($request);

        // 6. Si el idioma es inglés y la respuesta es HTML, traducir
        if ($locale === 'en' && $response instanceof Response) {
            $contentType = $response->headers->get('Content-Type', '');
            if (str_contains($contentType, 'text/html')) {
                $content = $response->getContent();
                if (is_string($content) && !empty($content)) {
                    $response->setContent(TranslationService::translateHtml($content));
                }
            }
        }

        return $response;
    }
}
