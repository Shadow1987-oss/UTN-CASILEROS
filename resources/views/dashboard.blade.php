@extends('plantilla')

@section('titulo', 'Tablero - UTNAY Lockers')

@section('contenido')
    <!-- Multimedia Section -->
    <div class="multimedia-section">
        <div class="card">
            <h2> Sistema de Casilleros UTNAY</h2>

    <div class="card">
        <h2>Resumen general</h2>
        <p class="muted">Vista rápida del sistema actual.</p>
        <div class="grid grid-3" style="margin-top: 16px;">
            <div class="stat">
                <h3>Estudiantes</h3>
                <p>{{ $students }}</p>
            </div>
            <div class="stat">
                <h3>Casilleros</h3>
                <p>{{ $lockers }}</p>
            </div>
            <div class="stat">
                <h3>Períodos</h3>
                <p>{{ $periods }}</p>
            </div>
            <div class="stat">
                <h3>Asignaciones activas</h3>
                <p>{{ $active_assignments }}</p>
            </div>
        </div>
    </div>
            <!-- Video Institucional -->
            <div style="margin-top: 24px;">
                <h3> Guia de funcionamiento</h3>
                <p class="muted">Conoce más sobre como funciona el sistema de casilleros</p>
                <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; background: #000; box-shadow: var(--shadow);">
                    <iframe
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 12px;"
                        src="https://www.youtube.com/embed/ScMzIvxBSi4"
                        title="Video Institucional UTNAY"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Geolocalización -->
    <div class="card" style="margin-top: 24px;">
        <div style="text-align: center; margin-bottom: 16px;">
            <h2 style="margin-bottom: 8px;"> Geolocalización con un marcador de Google Maps</h2>
        </div>
        <div id="mapa"></div>
        <div style="text-align: center; margin-top: 16px;">
            <p class="muted">Ubicación de la Universidad Tecnológica de Nayarit</p>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Función que permite inicializar el mapa
    function initMap(){
        // Coordenadas de la UTNAY
        var utn = { lat: 21.424081270651673, lng: -104.89837987466123 };

        // Ubicación en el mapa
        var map = new google.maps.Map(document.getElementById('mapa'), {
            zoom: 15,
            center: utn,
            mapTypeId: 'roadmap'
        });

        // Marcador en el mapa
        var marker = new google.maps.Marker({
            position: utn,
            map: map,
            title: 'Universidad Tecnológica de Nayarit'
        });

        // Ventana de información
        var infowindow = new google.maps.InfoWindow({
            content: `<strong>Universidad Tecnológica de Nayarit</strong><br>
                      Xalisco, Nayarit<br>
                      TSU en Desarrollo de Software Multiplataforma`
        });

        marker.addListener('click', function() {
            infowindow.open(map, marker);
        });
    }
</script>

<!-- Google Maps API -->
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCMqz5JHcQV4yEFD7wkTWqrocIgp2ixNOk&callback=initMap">
</script>
@endsection
