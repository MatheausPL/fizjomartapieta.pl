const routes = {
    'o-mnie': {
        title: 'O mnie',
        html: `
            <div class="card">
                <header class="section-header">
                    <span class="section-tag">Poznajmy się</span>
                    <p>Fizjoterapia manualna oraz terapia ruchowa dopasowana indywidualnie do Twojego ciała i stylu życia.</p>
                </header>
                <div class="hero">
                    <div class="hero__content">
                        <div class="hero__text">
                            <p>Nazywam się Marta Pięta i jestem fizjoterapeutką z ponad 10-letnim stażem. Pracuję z pacjentami z dolegliwościami bólowymi narządu ruchu, po urazach oraz z problemami ortopedycznymi i przewlekłym bólem.</p>
                            <p>W swojej pracy łączę terapię manualną, ćwiczenia terapeutyczne oraz indywidualnie dobrane metody leczenia. Zależy mi nie tylko na zmniejszeniu bólu, ale przede wszystkim na znalezieniu jego przyczyny i przywróceniu pełnej sprawności.</p>
                            <p>Obecnie rozwijam swoje kompetencje w zakresie fizjoterapii uroginekologicznej, aby kompleksowo wspierać kobiety na różnych etapach życia. Regularnie uczestniczę w kursach i szkoleniach, dzięki czemu mogę stosować nowoczesne metody terapii.</p>
                            <p>Największą satysfakcję daje mi obserwowanie, jak dzięki wspólnej pracy pacjenci odzyskują komfort życia i wracają do aktywności.</p>
                        </div>
                    </div>
                    <div class="hero__panel">
                        <img class="hero__image" src="img/photos/marta.webp"
                             alt="Marta Pięta - fizjoterapeutka"
                             width="400" height="250"
                             loading="lazy" decoding="async" />
                        <h3>Na czym skupiam się</h3>
                        <ul class="hero__list">
                            <li>Fizjoterapia manualna i powięziowa</li>
                            <li>Fizjoterapia blizn po epizjotomii i CC</li>
                            <li>Fizjoterapia kobiet w ciąży</li>
                            <li>Rehabilitacja ortopedyczna</li>
                            <li>Trening medyczny</li>
                            <li>Rehabilitacja przed i pooperacyjna</li>
                        </ul>
                    </div>
                </div>
            </div>
        `
    },
    'pierwsza-wizyta': {
        title: 'Pierwsza wizyta',
        html: `
            <div class="card"> 
                <span class="section-tag">
                    <h2>Pierwsza wizyta</h2>
                    <p>Pierwsza wizyta to spokojne, uporządkowane spotkanie, podczas którego poznaję Twoje potrzeby, wykonuję delikatną ocenę funkcjonalną i ustalam plan terapii dopasowany do Ciebie.</p>
                </span>
                <div class="methods-grid">
                    <article class="method-card">
                        <div class="method-header">
                            <span class="content__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 10a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 14.286V4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                    <path d="M20 9a2 2 0 0 1 2 2v10.286a.71.71 0 0 1-1.212.502l-2.202-2.202A2 2 0 0 0 17.172 19H10a2 2 0 0 1-2-2v-1"/>
                                </svg>
                            </span>
                            <h3>Rozmowa i poznanie Twoich potrzeb</h3>
                        </div>
                        <p>Spokojna rozmowa, w której pytam o dolegliwości, ich historię, Twój styl życia, obawy i cele. Uważnie słucham, aby zrozumieć, czego naprawdę potrzebujesz — bez pośpiechu i w bezpiecznej atmosferze.</p>
                    </article>
                    <article class="method-card">
                        <div class="method-header">
                            <span class="content__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/>
                                    <path d="M3.22 13H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"/>
                                </svg>
                            </span>
                            <h3>Ocena funkcjonalna</h3>
                        </div>
                        <p>Badanie obejmuje ocenę zakresów ruchu, napięć mięśniowych, wzorców ruchowych oraz sposobu oddychania. Na każdym etapie informuję, co robię i dlaczego, abyś czuł/a się w pełni komfortowo.</p>
                    </article>
                    <article class="method-card">
                        <div class="method-header">
                            <span class="content__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10 5H3"/><path d="M12 19H3"/>
                                    <path d="M14 3v4"/><path d="M16 17v4"/>
                                    <path d="M21 12h-9"/><path d="M21 19h-5"/>
                                    <path d="M21 5h-7"/><path d="M8 10v4"/>
                                    <path d="M8 12H3"/>
                                </svg>
                            </span>
                            <h3>Terapia dopasowana do Ciebie</h3>
                        </div>
                        <p>W zależności od potrzeb łączę różne formy pracy — terapia jest indywidualna i nieoparta na sztywnych schematach. Dostosowuję tempo i intensywność do Twojego aktualnego samopoczucia.</p>
                    </article>
                    <article class="method-card">
                        <div class="method-header">
                            <span class="content__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M13 5h8"/>
                                    <path d="M13 12h8"/><path d="M13 19h8"/>
                                    <path d="m3 17 2 2 4-4"/>
                                    <rect x="3" y="4" width="6" height="6" rx="1"/>
                                </svg>
                            </span>
                            <h3>Wskazówki po wizycie</h3>
                        </div>
                        <p>Na koniec otrzymujesz proste, realne do wykonania ćwiczenia oraz wskazówki jak dbać o ciało na co dzień. W razie pytań po wizycie pozostaję do Twojej dyspozycji.</p>
                    </article>
                </div>
            </div>
        `
    },
    'jak-pracuje': {
        title: 'Jak pracuję',
        html: `
            <div class="card"> 
                <div class="section-tag">
                    <h2>Jak pracuję</h2>
                    <p>W pracy wykorzystuję sprawdzone, bezpieczne i skuteczne metody terapeutyczne. Każdą z nich dobieram indywidualnie do Twojego aktualnego stanu i celów.</p>
                </div> 
                <div class="methods-grid">
                    <article class="method-card">
                        <h3>Terapia manualna</h3>
                        <p>Praca z tkankami miękkimi, stawami i powięzią w celu redukcji bólu, poprawy ruchomości i przywrócenia prawidłowych wzorców ruchowych.</p>
                    </article>
                    <article class="method-card">
                        <h3>Terapia powięziowa</h3>
                        <p>Delikatne, precyzyjne techniki wpływające na napięcia w powięzi, poprawiające elastyczność tkanek i zmniejszające ból.</p>
                    </article>
                    <article class="method-card">
                        <h3>Suche igłowanie</h3>
                        <p>Technika wykorzystująca cienkie igły do pracy z punktami spustowymi, redukcji napięcia mięśniowego i poprawy funkcji tkanek.</p>
                    </article>
                    <article class="method-card">
                        <h3>Trening medyczny</h3>
                        <p>Ćwiczenia ukierunkowane na poprawę stabilizacji, siły, mobilności oraz bezpieczny powrót do pełnej sprawności po urazach.</p>
                    </article>
                    <article class="method-card method-card--wide">
                        <h3>Kinesiotaping</h3>
                        <p>Aplikacja elastycznych taśm wspierająca pracę mięśni, redukująca obrzęki i poprawiająca mikrokążenie oraz stabilizację bez ograniczania ruchu.</p>
                    </article>
                </div>
            </div>
        `
    },
    'gdzie-pracuje': {
        title: 'Gdzie pracuję',
        html: `
            <div class="card">
                <span class="section-tag">
                        <h2>Lokalizacja i dojazd</h2>
                        <p>Pracuję w Jaworznie i okolicach, oferując zarówno dogodne wizyty domowe, jak i przyjęcia w profesjonalnym gabinecie.</p>
                </span> 
                <div class="location-grid">
                    <article class="location-card">
                        <div> 
                            <h3> 
                                <span class="content__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/>
                                        <path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    </svg>
                                </span>Wizyty domowe
                            </h3>
                            <p>Dojeżdżam do pacjentów w komfortowym środowisku domowym — bez pośpiechu i w przyjaznej atmosferze.</p>
                        </div>
                        <ul>
                            <li><span>Obszar dojazdu</span><strong>Jaworzno, Sosnowiec, Chrzanów, Mysłowice i okolice</strong></li>
                            <li><span>Forma terapii</span><strong>Wygodna rehabilitacja u Ciebie w domu</strong></li>
                        </ul>
                    </article>
                    <article class="location-card">
                        <div>
                            <h3> 
                                <span class="content__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 7v4"/>
                                        <path d="M14 21v-3a2 2 0 0 0-4 0v3"/><path d="M14 9h-4"/><path d="M18 11h2a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2h2"/>
                                        <path d="M18 21V5a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16"/>
                                    </svg>
                                </span>Gabinet Delmed Clinic
                            </h3>
                            <p>Przyjmuję w gabinecie raz w tygodniu, jeśli wolisz terapię w warunkach klinicznych.</p>
                        </div>
                        <ul>
                            <li><span>Placówka</span><strong>Delmed Clinic - ul. Bogusławskiego 5, 43-600 Jaworzno</strong></li>
                            <li><span>Strona WWW</span><strong><a href="https://delmed.pl/poradnie/fizjoterapia/" target="_blank" rel="noopener noreferrer">DELMED Clinic</a></strong></li>
                        </ul>
                    </article>
                </div>
            </div>
        `
    },
    cennik: {
        title: 'Cennik',
        html: `
            <div class="card">
                <span class="section-tag">
                        <h2>Cennik</h2>
                        <p>Wszystkie wizyty obejmują indywidualną ocenę oraz terapię dopasowaną bezpośrednio do Twoich potrzeb.</p>
                </span> 
                <div class="pricing-grid">
                    <article class="pricing-card"> 
                        <h3>Wizyta w domu pacjenta</h3>  
                        <span class="pricing-price">180 zł</span>
                        <p class="pricing-note">Kompleksowa terapia w komfortowym środowisku domowym wraz z dojazdem.</p>
                    </article>
                    <article class="pricing-card"> 
                        <h3>Wizyta w gabinecie</h3>
                        <span class="pricing-price">160 zł</span> 
                        <p class="pricing-note">Ocena funkcjonalna, terapia manualna oraz plan dalszej pracy w gabinecie.</p>
                    </article>
                    <article class="pricing-card"> 
                            <h3>Suche igłowanie / Terapia powięziowa</h3>
                            <span class="pricing-price">150 zł</span> 
                        <p class="pricing-note">Specjalistyczne techniki celowane wspierające terapię manualną i zniesienie bólu.</p>
                    </article>
                    <article class="pricing-card"> 
                            <h3>Trening medyczny</h3>
                            <span class="pricing-price">160 zł</span> 
                        <p class="pricing-note">Indywidualne ćwiczenia terapeutyczne dostosowane do Twojego celu sprawnościowego.</p>
                    </article>
                    <article class="pricing-card pricing-card--wide"> 
                            <h3>Kinesiotaping</h3>
                            <span class="pricing-price">20–40 zł</span> 
                        <p class="pricing-note">Zaopatrzenie taśmami i profesjonalna aplikacja zależnie od obszaru zabiegowego.</p>
                    </article>
                </div>
            </div>
        `
    },
    kontakt: {
        title: 'Kontakt',
        html: `
            <div class="card">
                <span class="section-tag">
                        <h2>Napisz lub zadzwoń</h2>
                        <p>Jeśli chcesz umówić wizytę lub uzyskać dodatkowe informacje, skontaktuj się ze mną bezpośrednio.</p>
                </span>
                <div class="contact-grid">
                    <div class="contact-card">
                        <h3>Informacje o praktyce</h3>
                        <ul>
                            <li><span>Firma</span><strong>Fizjoterapia Marta Pięta</strong></li>
                            <li><span>Numer PWZFz</span><strong>19460</strong></li>
                            <li><span>NIP</span><strong>6321975004</strong></li>
                            <li><span>REGON</span><strong>529779494</strong></li>
                        </ul>
                    </div>
                    <div class="contact-card">
                        <h3>Dane kontaktowe</h3>
                        <ul>
                            <li><span>Telefon</span><strong><a href="tel:+48453482415">+48 453 482 415</a></strong></li>
                            <li><span>E-mail</span><strong><a href="mailto:fizjo.marta.pieta@outlook.com">fizjo.marta.pieta@outlook.com</a></strong></li>
                            <li><span>Rezerwacja</span><strong>Telefonicznie lub e-mailowo</strong></li>
                            <li><span>Terminy</span><strong>Ustalane indywidualnie</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        `
    }
};

// === SEO meta descriptions per route ===
const routeMeta = {
    'o-mnie': 'Fizjoterapia Marta Pięta – mgr fizjoterapii z 10-letnim stażem. Terapia manualna, fizjoterapia uroginekologiczna, rehabilitacja ortopedyczna w Jaworznie.',
    'pierwsza-wizyta': 'Pierwsza wizyta u fizjoterapeutki Marty Pięty – co Cię czeka? Rozmowa, ocena funkcjonalna i indywidualny plan terapii dopasowany do Twoich potrzeb.',
    'jak-pracuje': 'Metody terapeutyczne: terapia manualna, powięziowa, suche igłowanie, trening medyczny i kinesiotaping. Fizjoterapia Marta Pięta, Jaworzno.',
    'gdzie-pracuje': 'Wizyty domowe: Jaworzno, Sosnowiec, Chrzanów, Mysłowice. Gabinet: Delmed Clinic. Fizjoterapia Marta Pięta – terapia w domu lub w gabinecie.',
    'cennik': 'Cennik fizjoterapii: wizyta domowa 180 zł, wizyta w gabinecie 160 zł, suche igłowanie 150 zł, trening medyczny 160 zł, kinesiotaping 20–40 zł.',
    'kontakt': 'Skontaktuj się z fizjoterapeutką Martą Piętą. Telefon: +48 453 482 415, e-mail: fizjo.marta.pieta@outlook.com. Umów wizytę w Jaworznie lub jako wizytę domową.'
};

// === Page transition: fade-out + update + fade-in ===
let isTransitioning = false;

function renderRoute(routeName = 'o-mnie') {
    const view = document.getElementById('view');
    const route = routes[routeName] || routes['o-mnie'];

    if (!view) return;

    if (isTransitioning) return;
    isTransitioning = true;

    // Fade out
    view.classList.add('view--exit');

    setTimeout(() => {
        // Update content
        view.innerHTML = route.html;
        view.classList.remove('view--exit');
        view.classList.add('view--enter');

        // Update SEO title & meta description
        document.title = `Fizjoterapia Marta Pięta | ${route.title}`;
        const metaDesc = document.getElementById('meta-description');
        if (metaDesc && routeMeta[routeName]) {
            metaDesc.setAttribute('content', routeMeta[routeName]);
        }

        // Update active nav link
        document.querySelectorAll('.sidebar__nav a').forEach(link => {
            link.classList.toggle('active', link.getAttribute('data-route') === routeName);
        });

        // Scroll content area to top
        const content = document.querySelector('.content');
        if (content) content.scrollTop = 0;
        window.scrollTo({ top: 0, behavior: 'smooth' });

        requestAnimationFrame(() => {
            view.classList.remove('view--enter');
            isTransitioning = false;
        });
    }, 200);
}

function updateRouteFromHash() {
    const hash = window.location.hash.replace('#', '').trim();
    const routeName = hash || 'o-mnie';
    renderRoute(routeName);
}

document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('.sidebar__toggle');
    const toggleIcon = document.querySelector('.toggle-icon');
    const sidebar = document.querySelector('.sidebar');
    const fab = document.getElementById('fab-call');

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('sidebar--open');
        if (navToggle) navToggle.setAttribute('aria-expanded', 'false');
        if (toggleIcon) toggleIcon.textContent = '☰';
    }

    if (navToggle && sidebar) {
        navToggle.addEventListener('click', () => {
            const isOpen = sidebar.classList.toggle('sidebar--open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (toggleIcon) toggleIcon.textContent = isOpen ? '✕' : '☰';
        });
    }

    document.querySelectorAll('.sidebar__nav a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 700) closeSidebar();
        });
    });

    window.addEventListener('hashchange', () => {
        updateRouteFromHash();
        if (window.innerWidth <= 700) closeSidebar();
    });

    // === FAB: show after scrolling 200px on mobile ===
    if (fab) {
        const handleFabVisibility = () => {
            if (window.innerWidth <= 700) {
                if (window.scrollY > 120) {
                    fab.classList.add('fab-call--visible');
                } else {
                    fab.classList.remove('fab-call--visible');
                }
            } else {
                fab.classList.remove('fab-call--visible');
            }
        };
        window.addEventListener('scroll', handleFabVisibility, { passive: true });
        window.addEventListener('resize', handleFabVisibility, { passive: true });
    }

    updateRouteFromHash();
});

