const routes = {
    'o-mnie': {
        title: 'O mnie',
        render: renderOMnie
    },
	'pierwsza-wizyta': {
    title: 'Pierwsza wizyta',
    render: renderPierwszaWizyta
	},
    'jak-pracuje': {
        title: 'Jak pracuję',
        render: renderJakPracuje
    },
	'gdzie-pracuje': {
    title: 'Gdzie pracuję',
    render: renderGdziePracuje
	},
	'opinie': {
        title: 'Opinie',
        render: renderOpinie
    },
    'cennik': {
        title: 'Cennik',
        render: renderCennik
    },
    'kontakt': { 
		title: 'Kontakt', 
		render: renderKontakt 
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

// Pomocnicza funkcja zabezpieczająca przed błędami HTML i atakami XSS
function escapeHTML(str) {
    if (!str) return '';
    return str.toString().replace(/[&<>'"]/g, 
        tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
    );
}

function renderCennik(data) {
    // Odczyt z rozbitego pliku data/cennik.json (brak otoczki data.cennik, dane są od razu w obiekcie)
    const c = data.cennik ? data.cennik : data;
    const items = Array.isArray(c.items) ? c.items : [];

    return `
        <div class="card">
            <span class="section-tag">
                <h2>${escapeHTML(c.title || 'Cennik')}</h2>
                <p>${escapeHTML(c.subtitle || '')}</p>
            </span>
            <div class="pricing-grid">
                ${items.map(item => `
                    <article class="pricing-card">
                        <h3>${escapeHTML(item.name || '')}</h3>
                        <span class="pricing-price">${escapeHTML(item.price || '')}</span>
                        <p class="pricing-note">${escapeHTML(item.desc || '')}</p>
                    </article>
                `).join('')}
            </div>
        </div>
    `;
}

function renderOMnie(data) {
    const om = data.o_mnie ? data.o_mnie : data;
    const paragraphs = Array.isArray(om.items) ? om.items : [];
    const focusItems = Array.isArray(om.focus_items) ? om.focus_items : [];

    return `
        <div class="card">
            <span class="section-tag">
                <h2>${escapeHTML(om.subtitle || 'Poznajmy się')}</h2>
                <p>${escapeHTML(om.header_text || '')}</p>
            </span> 
            <div class="hero">
                <div class="hero__content">
                    <div class="hero__text">
                        ${paragraphs.map(item => `<p>${parseMarkdownToHtml(item.text || '')}</p>`).join('')}
                    </div>
                </div>
                <div class="hero__panel">
                    <img class="hero__image" src="${escapeHTML(om.image || 'img/photos/marta.webp')}"
                         alt="Marta Pięta - fizjoterapeutka"
                         loading="lazy" decoding="async" />
                    
                    ${focusItems.length > 0 ? `
                        <h3>${escapeHTML(om.focus_title || 'Na czym skupiam się')}</h3>
                        <ul class="hero__list">
                            ${focusItems.map(item => `<li>${escapeHTML(item.text || '')}</li>`).join('')}
                        </ul>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

function renderPierwszaWizyta(data) {
    // Odczyt z rozbitego, płaskiego pliku JSON
    const pv = data.pierwsza_wizyta ? data.pierwsza_wizyta : data;
    const steps = Array.isArray(pv.steps) ? pv.steps : [];

    return `
        <div class="card"> 
            <span class="section-tag">
                <h2>${escapeHTML(pv.title || 'Pierwsza wizyta')}</h2>
                <p>${escapeHTML(pv.subtitle || '')}</p>
            </span>
            <div class="methods-grid">
                ${steps.map(step => `
                    <article class="method-card ${step.wide ? 'method-card--wide' : ''}">
                        <div class="method-header"> 
                            ${step.has_icon 
                                ? `<span class="content__icon">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                           ${step.svg_inner || ''}
                                       </svg>
                                   </span>`
                                : ''
                            }
                            <h3>${escapeHTML(step.title || '')}</h3>
                        </div>
                        <p>${parseMarkdownToHtml(step.desc || '')}</p>
                    </article>
                `).join('')}
            </div>
        </div>
    `;
}


function renderJakPracuje(data) {
    const jp = data.jak_pracuje ? data.jak_pracuje : data;
    const methods = Array.isArray(jp.methods) ? jp.methods : [];

    return `
        <div class="card"> 
            <div class="section-tag">
                <h2>${escapeHTML(jp.title || 'Jak pracuję')}</h2>
                <p>${escapeHTML(jp.subtitle || '')}</p>
            </div> 
            <div class="methods-grid">
                ${methods.map(method => `
                    <article class="method-card ${method.wide ? 'method-card--wide' : ''}">
                        <div class="method-header">
                            ${method.has_icon
                                ? `<span class="content__icon">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                           ${method.svg_inner || ''}
                                       </svg>
                                   </span>`
                                : ''
                            }
                            <h3>${escapeHTML(method.title || '')}</h3>
                        </div>
                        <p>${parseMarkdownToHtml(method.desc || '')}</p>
                    </article>
                `).join('')}
            </div>
        </div>
    `;
}

function renderGdziePracuje(data) {
    const gp = data.gdzie_pracuje ? data.gdzie_pracuje : data;
    const cards = Array.isArray(gp.cards) ? gp.cards : [];

    return `
        <div class="card">
            <span class="section-tag">
                <h2>${escapeHTML(gp.title || 'Lokalizacja i dojazd')}</h2>
                <p>${escapeHTML(gp.subtitle || '')}</p>
            </span> 
            <div class="location-grid">
                ${cards.map(card => {
                    const details = Array.isArray(card.details) ? card.details : [];
                    return `
                        <article class="location-card ${card.wide ? 'method-card--wide' : ''}">
                            <div class="method-header">  
            					${card.has_icon 
                					? `<span class="content__icon">
                       					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">					
									${card.svg_inner || ''}
                       				</svg>
                   				</span>`
                			: ''
            					}
									<h3>${escapeHTML(card.title || '')} </h3>
								</div>
                                <p>${escapeHTML(card.desc || '')}</p> 
                            <ul>
                                ${details.map(det => `
                                    <li>
                                        <span>${escapeHTML(det.label || '')}</span>
                                        <strong>
                                            ${det.is_link && det.url 
                                                ? `<a href="${escapeHTML(det.url)}" target="_blank" rel="noopener noreferrer">${escapeHTML(det.value || '')}</a>` 
                                                : escapeHTML(det.value || '')
                                            }
                                        </strong>
                                    </li>
                                `).join('')}
                            </ul>
                        </article>
                    `;
                }).join('')}
            </div>
        </div>
    `;
}



function renderKontakt(data) {
    const k = data.kontakt ? data.kontakt : data;
    const sections = Array.isArray(k.sections) ? k.sections : [];  

    return `
        <div class="card">
            <span class="section-tag">
                <h2>${escapeHTML(k.title || 'Napisz lub zadzwoń')}</h2>
                <p>${escapeHTML(k.subtitle || '')}</p>
            </span>
            <div class="contact-grid">
                ${sections.map(section => {
                    const fields = Array.isArray(section.fields) ? section.fields : [];
                    return `
                        <div class="contact-card ${section.wide ? 'method-card--wide' : ''}">
                            <div class="method-header">  
            					${section.has_icon 
                					? `<span class="content__icon">
                       					<svg xmlns="http://w3.org" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                           			${section.svg_inner || ''}
                       				</svg>
                   				</span>`
                			: ''
            					}
				 			<h3>${escapeHTML(section.title || '')}</h3>
		</div>
                            <ul>
                                ${fields.map(field => {
                                    let contentHtml = '';
                                    const type = field.type || 'text';
                                    const val = field.value || '';
                                    const lVal = field.link_value || '';

                                    // Dynamicznie generujemy bezpieczny kod HTML dla linków
                                    if (type === 'tel' && lVal) {
                                        contentHtml = `<a href="tel:${escapeHTML(lVal)}">${escapeHTML(val)}</a>`;
                                    } else if (type === 'email' && lVal) {
                                        contentHtml = `<a href="mailto:${escapeHTML(lVal)}">${escapeHTML(val)}</a>`;
                                    } else if (type === 'link' && lVal) {
                                        contentHtml = `<a href="${escapeHTML(lVal)}" target="_blank" rel="noopener noreferrer">${escapeHTML(val)}</a>`;
                                    } else {
                                        contentHtml = escapeHTML(val);
                                    }

                                    return `
                                        <li>
                                            <span>${escapeHTML(field.label || '')}</span>
                                            <strong>${contentHtml}</strong>
                                        </li>
                                    `;
                                }).join('')}
                            </ul>
                        </div>
                    `;
                }).join('')}
            </div>
        </div>
    `;
}

function renderOpinie(data) {
    // 1. Pobieramy surowe skrypty z bazy JSON i czyścimy ukośniki ucieczki z PHP json_encode
    const googleCode = (data.google_script || '').replace(/\\\//g, '/');
    const facebookCode = (data.facebook_script || '').replace(/\\\//g, '/');

    // 2. NATYCHMIASTOWE URUCHOMIENIE (Bez setTimeout) - Wstrzykiwanie skryptu od razu po kliknięciu zakładki
    // Przeglądarka ma już pobrany plik z RAMu dzięki regule preload w index.html
    const containerId = 'trustindex-dynamic-wrapper';
    
    // Uruchamiamy mikro-asynchroniczność (0ms), aby silnik najpierw zwrócił strukturę HTML, a linijkę niżej od razu wstrzyknął skrypt
    setTimeout(() => {
        const container = document.getElementById(containerId);
        if (!container) return;

        // Pomocnicza funkcja parsująca i instalująca skrypt loader.js prosto od Trustindex
        const injectTrustindexLoader = (rawScriptHtml, targetBoxId) => {
            if (!rawScriptHtml) return;

            const targetBox = document.getElementById(targetBoxId);
            if (!targetBox) return;

            // Tworzymy wirtualny kontener, aby wyciągnąć parametry z wklejonego kodu
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = rawScriptHtml;
            const srcScript = tempDiv.querySelector('script');

            if (srcScript) {
                const s = document.createElement('script');
                s.src = srcScript.getAttribute('src');
                s.defer = true;
                s.async = true;

                // Jeśli kod posiadał atrybut data-id, przepisujemy go
                if (srcScript.getAttribute('data-id')) {
                    s.setAttribute('data-id', srcScript.getAttribute('data-id'));
                }

                // Wstrzykujemy żywy tag skryptu do przygotowanego kontenera na stronie Marty
                targetBox.appendChild(s);
            }
        };

        // Uruchamiamy ładowanie widżetów w przeznaczonych dla nich boksach HTML
        if (googleCode) {
            injectTrustindexLoader(googleCode, 'ti-google-target');
        }
        if (facebookCode) {
            injectTrustindexLoader(facebookCode, 'ti-facebook-target');
        }
    }, 0);

    // 3. Zwracamy czysty, oficjalny szkielet HTML, którego darmowe skrypty Trustindex potrzebują do wyświetlenia klocków
    return `
        <div class="card">
            <span class="section-tag">
                <h2>Opinie naszych pacjentów</h2>
                <p>Zobacz autentyczne i zweryfikowane recenzje pobrane automatycznie z profili społecznościowych gabinetu.</p>
            </span>
            
            <!-- POPRAWKA: display: block eliminuje rozpychanie flexa w pionie, a margin-top został naprawiony z opx na 0px -->
            <div id="trustindex-dynamic-wrapper" style="margin-top: 0px; display: block; width: 100%;">
                ${(!googleCode && !facebookCode) 
                    ? `<p style="text-align:center; color:#94a3b8; font-style:italic; padding: 40px 0;">Brak skonfigurowanych widżetów opinii. Przejdź do panelu administratora i wklej kody skryptów.</p>`
                    : `
                        <!-- ODRĘBNE KONTENERY DLA GOOGLE I FACEBOOKA (W NIE WSTRZYKUJEMY SCRIPT) -->
                        ${googleCode ? `
                            <div style="margin-bottom: 20px; width: 100%;">
                                <h3 style="font-size: 1.1rem; color: #475569; margin-bottom: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px; font-family: Arial, sans-serif;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#ea4335"><path d="M12.24 10.285V13.4h6.887c-.275 1.565-1.88 4.604-6.887 4.604-4.33 0-7.866-3.577-7.866-8s3.536-8 7.866-8c2.46 0 4.105 1.025 5.047 1.926l2.427-2.334C17.955 2.192 15.34 1 12.24 1 6.033 1 1 6.033 1 12.24s5.033 11.24 11.24 11.24c6.478 0 10.793-4.537 10.793-10.997 0-.746-.08-1.32-.176-1.705H12.24z"/></svg>
                                    Opinie z profilu Google
                                </h3>
                                <div id="ti-google-target" style="width: 100%;"></div>
                            </div>
                        ` : ''}

                        ${facebookCode ? `
                            <div style="width: 100%;">
                                <h3 style="font-size: 1.1rem; color: #475569; margin-bottom: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px; font-family: Arial, sans-serif;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#1877f2"><path d="M24 12a12 12 0 1 0-13.875 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385A12 12 0 0 0 24 12z"/></svg>
                                    Recenzje z profilu Facebook
                                </h3>
                                <div id="ti-facebook-target" style="width: 100%;"></div>
                            </div>
                        ` : ''}
                    `
                }
            </div>
        </div>
    `;

}


function parseMarkdownToHtml(rawText) {
    if (!rawText) return '';
    
    // 1. Zabezpieczenie przed wstrzykiwaniem kodu
    let cleanText = rawText
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");

    let lines = cleanText.split('\n');
    let inList = false;
    let resultHtml = [];

    lines.forEach(line => {
        let trimmedLine = line.trim();
        
        // Jeśli linia zaczyna się od "* " (gwiazdka i spacja), owijamy w naszą nową klasę CSS
        if (trimmedLine.startsWith('* ')) {
            if (!inList) {
                resultHtml.push('<ul class="custom-bullet-list">');
                inList = true;
            }
            let content = trimmedLine.substring(2);
            resultHtml.push(`<li>${content}</li>`);
        } else {
            if (inList) {
                resultHtml.push('</ul>');
                inList = false;
            }
            resultHtml.push(line);
        }
    });

    if (inList) {
        resultHtml.push('</ul>');
    }

    // Łączenie linii i zamiana enterów na łamanie wiersza <br>
    return resultHtml.join('\n').replace(/\n/g, '');
}


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
        // Dynamiczne dobieranie ścieżki do osobnych plików JSON w folderze data/
        let jsonFile = null;
        if (routeName === 'o-mnie') jsonFile = 'data/o-mnie.json';
        if (routeName === 'cennik') jsonFile = 'data/cennik.json';
		if (routeName === 'pierwsza-wizyta') jsonFile = 'data/pierwsza-wizyta.json';
		if (routeName === 'jak-pracuje') jsonFile = 'data/jak-pracuje.json';
		if (routeName === 'gdzie-pracuje') jsonFile = 'data/gdzie-pracuje.json';
		if (routeName === 'opinie') jsonFile = 'data/opinie.json';
		if (routeName === 'kontakt') jsonFile = 'data/kontakt.json';
		
        // Jeśli strona potrzebuje bazy danych, pobieramy właściwy plik JSON
        if (jsonFile) {
    		// DYNAMICZNY ZNACZNIK CZASU (Cache Busting) - Wymusza pobranie najnowszego JSON u każdego pacjenta
    		const secureJsonUrl = `${jsonFile}?v=${new Date().getTime()}`;

    		fetch(secureJsonUrl)
        		.then(r => {
            		if (!r.ok) throw new Error("Błąd pobierania pliku: " + jsonFile);
            		return r.json();
        		})
        		.then(json => {
            		const html = route.render ? route.render(json) : route.html;
            		view.innerHTML = html;
            		finalizeRouteRender(view, route, routeName);
        		})
        		.catch(err => {
            		console.error("Błąd ładowania danych JSON dla: " + jsonFile, err);
            		// Awaryjny fallback z pustym obiektem, by strona nie była biała
            		const html = route.render ? route.render({}) : route.html;
            		view.innerHTML = html;
            		finalizeRouteRender(view, route, routeName);
        		});
			} else {
    		// Jeśli podstrona jest statyczna i nie ma pliku JSON, ładujemy czysty HTML
   			 view.innerHTML = route.html || '';
    		finalizeRouteRender(view, route, routeName);
			} 
    }, 200);
}

// Funkcja pomocnicza domykająca animacje, nagłówki SEO oraz wygląd menu bocznego
function finalizeRouteRender(view, route, routeName) {
    // Animacje wejścia
    view.classList.remove('view--exit');
    view.classList.add('view--enter');

    // Aktualizacja tytułu i meta description dla SEO
    document.title = `Fizjoterapia Marta Pięta | ${route.title}`;
    const metaDesc = document.getElementById('meta-description');
    if (metaDesc && routeMeta[routeName]) {
        metaDesc.setAttribute('content', routeMeta[routeName]);
    }

    // Podświetlenie aktywnego linku w menu bocznym strony
    document.querySelectorAll('.sidebar__nav a').forEach(link => {
        link.classList.toggle('active', link.getAttribute('data-route') === routeName);
    });

    // Płynne przewijanie na samą górę sekcji
    const contentEl = document.querySelector('.content');
    if (contentEl) contentEl.scrollTop = 0;
    window.scrollTo({ top: 0, behavior: 'smooth' });

    requestAnimationFrame(() => {
        view.classList.remove('view--enter');
        isTransitioning = false;
    });
}

function updateRouteFromHash() {
    const hash = window.location.hash.replace('#', '').trim();
    const routeName = hash || 'o-mnie';
    renderRoute(routeName);
}

// Inicjalizacja zdarzeń po pełnym załadowaniu drzewa DOM
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

    // === FAB: Pokaż pływający przycisk po przewinięciu 120px na telefonach ===
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

    // Pierwsze uruchomienie routingu po wejściu na stronę
    updateRouteFromHash();
});
