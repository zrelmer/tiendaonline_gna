@if ($brandMarqueeItems->isNotEmpty())
    <section class="home-brand-marquee section-b-space" aria-label="Con la confianza de marcas líderes">
        <div class="container-fluid-lg">
            <div class="title text-center">
                <h2>Con la confianza de marcas líderes, entre ellas</h2>
            </div>

            <div class="home-brand-marquee__band">
                <div class="home-brand-marquee__viewport">
                    <div class="home-brand-marquee__track"
                         style="--marquee-items: {{ $brandMarqueeItems->count() }};">
                        @foreach ([1, 2] as $loopPass)
                            <ul class="home-brand-marquee__group" @if ($loopPass === 2) aria-hidden="true" @endif>
                                @foreach ($brandMarqueeItems as $marcaItem)
                                    <li class="home-brand-marquee__item">
                                        <a href="{{ $marcaItem['shop_url'] }}"
                                           class="home-brand-marquee__link"
                                           @if ($loopPass === 2) tabindex="-1" @endif>
                                            <img src="{{ $marcaItem['logo_url'] }}"
                                                 class="home-brand-marquee__logo"
                                                 alt="{{ $marcaItem['nombre'] }}"
                                                 loading="lazy"
                                                 decoding="async">
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
