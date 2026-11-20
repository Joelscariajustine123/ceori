/**
 * CEORI Skintiva – front-end interactions (no dependencies).
 *
 * Header state, scroll reveals, back-to-top, specialists scroller, testimonial
 * carousel, results gallery (filters, lightbox, comparison slider), appointment
 * form submission and share-link copying. Everything degrades gracefully without JS.
 */
( function () {
	'use strict';

	const settings = window.ceoriTheme || {};
	const i18n = settings.i18n || {};
	const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	const $$ = ( selector, root ) => Array.from( ( root || document ).querySelectorAll( selector ) );

	/* In-page links: sections below the screen are not laid out yet (content-visibility), so
	   their real heights would only appear mid-scroll and the jump would miss its target.
	   Lay everything out first – on click, or when the page opens with a #fragment. */
	const jumpTarget = ( hash ) => {
		try {
			return hash.length > 1 ? document.getElementById( decodeURIComponent( hash.slice( 1 ) ) ) : null;
		} catch ( e ) {
			return null;
		}
	};
	const jumpTo = ( target, smooth ) => {
		document.documentElement.classList.add( 'is-jumping' );
		// window.scrollTo, not scrollIntoView: on phones scrollIntoView can be ignored right after
		// the side panel closes (tapped link inside the panel).
		const go = ( behavior ) => {
			const pad = parseFloat( getComputedStyle( document.documentElement ).scrollPaddingTop ) || 0;
			window.scrollTo( { top: target.getBoundingClientRect().top + window.scrollY - pad, behavior } );
		};
		go( smooth && ! reduceMotion ? 'smooth' : 'auto' );
		// A long smooth scroll can stop short on phones (the page settles while it runs):
		// once the page has stopped moving, finish the jump exactly.
		let last = -1;
		let checks = 0;
		const watch = window.setInterval( () => {
			const y = window.scrollY;
			checks += 1;
			if ( y !== last && checks < 20 ) {
				last = y;
				return;
			}
			window.clearInterval( watch );
			const pad = parseFloat( getComputedStyle( document.documentElement ).scrollPaddingTop ) || 0;
			if ( Math.abs( target.getBoundingClientRect().top - pad ) > 4 ) {
				go( 'auto' );
			}
		}, 250 );
	};
	if ( jumpTarget( window.location.hash ) ) {
		window.requestAnimationFrame( () => jumpTo( jumpTarget( window.location.hash ), false ) );
	}
	document.addEventListener( 'click', ( event ) => {
		const link = event.target.closest && event.target.closest( 'a[href*="#"]' );
		if ( ! link || event.defaultPrevented || link.origin !== window.location.origin || link.pathname !== window.location.pathname || link.search !== window.location.search ) {
			return;
		}
		const target = jumpTarget( link.hash );
		// Only sections inside the page content (the skip link and back-to-top keep their own behaviour).
		if ( target && target.id !== 'content' && target.closest( 'main' ) ) {
			event.preventDefault();
			// From the side panel: close it first so the page can scroll.
			const panelClose = link.closest( '.ceori-offcanvas__panel' ) && document.querySelector( '.ceori-offcanvas__panel .ceori-offcanvas__close' );
			if ( panelClose ) {
				panelClose.click();
			}
			window.history.pushState( null, '', link.hash );
			if ( ! target.hasAttribute( 'tabindex' ) ) {
				target.setAttribute( 'tabindex', '-1' );
			}
			// Start once the panel has closed and the page is scrollable again.
			window.setTimeout( () => {
				target.focus( { preventScroll: true } );
				jumpTo( target, true );
			}, panelClose ? 60 : 0 );
		}
	} );

	/* Header shadow and back-to-top visibility */
	const header = document.querySelector( '.site-header-wrap' );
	const toTop = document.querySelector( '.ceori-to-top' );
	let ticking = false;
	const onScroll = () => {
		const y = window.scrollY;
		if ( header ) {
			header.classList.toggle( 'is-scrolled', y > 8 );
		}
		if ( toTop ) {
			toTop.classList.toggle( 'is-visible', y > 700 );
		}
		ticking = false;
	};
	window.addEventListener( 'scroll', () => {
		if ( ! ticking ) {
			window.requestAnimationFrame( onScroll );
			ticking = true;
		}
	}, { passive: true } );
	// First check after load (a reload can restore a scrolled position); reading scrollY while
	// the page is still laying out would force an early layout.
	if ( 'complete' === document.readyState ) {
		window.requestAnimationFrame( onScroll );
	} else {
		window.addEventListener( 'load', () => window.requestAnimationFrame( onScroll ), { once: true } );
	}

	if ( toTop ) {
		toTop.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			window.scrollTo( { top: 0, behavior: reduceMotion ? 'auto' : 'smooth' } );
			const target = document.querySelector( '.site-header__logo a, a, button' );
			if ( target ) {
				target.focus( { preventScroll: true } );
			}
		} );
	}

	/* Scroll reveal (Skintiva-style entrance): blocks fade in while sliding 40px –
	   from the left on the left side of the page, from the right on the right side,
	   and up from below when they are centred or full width.
	   Positions come from IntersectionObserver entries, so the page is never forced to
	   lay out synchronously while it loads (slow on phones). Headings and a few other
	   blocks only animate when they start below the fold. */
	const revealItems = $$( '.reveal, .ceori-section__head, .wp-block-image.ceori-intro__img, .ceori-tgrid__text, .ceori-contact__info, .ceori-scroller' )
		.filter( ( el ) => ( el.classList.contains( 'reveal' ) || ! el.closest( '.reveal' ) ) && ! el.matches( '.ceori-scroller__track > *' ) );
	// Carousel cards come in sideways: the row animates as a whole instead.
	$$( '.ceori-scroller__track > .reveal' ).forEach( ( el ) => el.classList.add( 'is-visible' ) );
	if ( revealItems.length ) {
		if ( reduceMotion || ! ( 'IntersectionObserver' in window ) ) {
			revealItems.forEach( ( el ) => el.classList.contains( 'reveal' ) && el.classList.add( 'is-visible' ) );
		} else {
			const seen = new WeakSet();
			const observer = new IntersectionObserver( ( entries ) => {
				entries.forEach( ( entry ) => {
					const el = entry.target;
					if ( ! seen.has( el ) ) {
						const r = entry.boundingClientRect;
						if ( ! r.width && ! r.height && ! entry.isIntersecting ) {
							return; // Inside a section not rendered yet (content-visibility): decide once it is.
						}
						seen.add( el );
						const vw = entry.rootBounds ? entry.rootBounds.width : window.innerWidth;
						if ( r.top >= window.innerHeight ) {
							// Below the fold: hide at once (no unseen fade-out), animate when scrolled to.
							el.style.transition = 'none';
							el.classList.add( 'reveal' );
							window.requestAnimationFrame( () => window.requestAnimationFrame( () => {
								el.style.transition = '';
							} ) );
						} else if ( ! el.classList.contains( 'reveal' ) ) {
							observer.unobserve( el ); // Already on screen (or passed): leave as is.
							return;
						}
						if ( window.innerWidth >= 1024 && ! /\breveal--/.test( el.className ) && r.width < vw * 0.6 ) {
							const centre = r.left + r.width / 2;
							if ( centre < vw * 0.42 ) {
								el.classList.add( 'reveal--left' );
							} else if ( centre > vw * 0.58 ) {
								el.classList.add( 'reveal--right' );
							}
						}
					}
					if ( entry.isIntersecting || entry.boundingClientRect.bottom < 0 ) {
						el.classList.add( 'is-visible' );
						observer.unobserve( el );
					}
				} );
			}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 } );
			revealItems.forEach( ( el ) => observer.observe( el ) );
			// Blocks inside a section the browser has not rendered yet are reported once with
			// an empty box and not again: observe them afresh when their section renders.
			$$( 'main .ceori-section' ).forEach( ( section ) => {
				section.addEventListener( 'contentvisibilityautostatechange', ( event ) => {
					if ( event.skipped ) {
						return;
					}
					revealItems.forEach( ( el ) => {
						if ( ! seen.has( el ) && section.contains( el ) ) {
							observer.unobserve( el );
							observer.observe( el );
						}
					} );
				} );
			} );
		}
	}

	/* About accordion (Skintiva): one item open at a time, sliding open and closed. */
	$$( '.ceori-acc' ).forEach( ( item ) => {
		const summary = item.querySelector( 'summary' );
		if ( ! summary ) {
			return;
		}
		const body = document.createElement( 'div' );
		body.className = 'ceori-acc__body';
		Array.from( item.children ).filter( ( c ) => c !== summary ).forEach( ( c ) => body.appendChild( c ) );
		item.appendChild( body );
		const animate = ( from, to, done ) => {
			if ( reduceMotion ) {
				done();
				return;
			}
			body.style.height = from + 'px';
			body.getBoundingClientRect(); // Commit the start height before animating.
			body.style.height = to + 'px';
			const end = ( e ) => {
				if ( e.target === body && 'height' === e.propertyName ) {
					body.removeEventListener( 'transitionend', end );
					done();
				}
			};
			body.addEventListener( 'transitionend', end );
		};
		item.ceoriClose = () => animate( body.scrollHeight, 0, () => {
			item.open = false;
			body.style.height = '';
		} );
		summary.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			if ( item.open ) {
				item.ceoriClose();
				return;
			}
			$$( '.ceori-acc[open]', item.parentElement ).forEach( ( other ) => other.ceoriClose && other.ceoriClose() );
			item.open = true;
			animate( 0, body.scrollHeight, () => {
				body.style.height = '';
			} );
		} );
	} );

	/* Specialists scroller */
	$$( '.ceori-doctors--carousel' ).forEach( ( wrap ) => {
		const track = wrap.querySelector( '.ceori-scroller__track' );
		const prev = wrap.querySelector( '[data-scroll="-1"]' );
		const next = wrap.querySelector( '[data-scroll="1"]' );
		if ( ! track || ! prev || ! next ) {
			return;
		}
		const update = () => {
			const max = track.scrollWidth - track.clientWidth - 2;
			prev.disabled = track.scrollLeft <= 2;
			next.disabled = track.scrollLeft >= max;
			wrap.querySelector( '.ceori-scroller__nav' ).hidden = max <= 0;
		};
		[ prev, next ].forEach( ( btn ) => btn.addEventListener( 'click', () => {
			const card = track.firstElementChild;
			const step = card ? card.getBoundingClientRect().width + 24 : track.clientWidth;
			track.scrollBy( { left: step * Number( btn.dataset.scroll ), behavior: reduceMotion ? 'auto' : 'smooth' } );
		} ) );
		track.addEventListener( 'scroll', update, { passive: true } );
		// Runs after layout (and on every resize), so loading never forces an early layout.
		if ( 'ResizeObserver' in window ) {
			new window.ResizeObserver( update ).observe( track );
		} else {
			window.addEventListener( 'resize', update );
			update();
		}
	} );

	/* Testimonials carousel */
	$$( '[data-carousel]' ).forEach( ( carousel ) => {
		const track = carousel.querySelector( '.ceori-testimonials__track' );
		const slides = $$( '.ceori-testimonials__page', carousel );
		const prev = carousel.querySelector( '[data-prev]' );
		const next = carousel.querySelector( '[data-next]' );
		const count = carousel.querySelector( '.ceori-testimonials__count' );
		if ( ! track || slides.length < 2 || ! prev || ! next ) {
			return;
		}
		let index = 0;
		const perView = () => 1;
		const render = () => {
			const max = Math.max( 0, slides.length - perView() );
			index = Math.min( Math.max( index, 0 ), max );
			const offset = slides[ index ].offsetLeft - slides[ 0 ].offsetLeft;
			track.style.transform = 'translateX(' + -offset + 'px)';
			slides.forEach( ( slide, i ) => {
				const visible = i >= index && i < index + perView();
				slide.setAttribute( 'aria-hidden', visible ? 'false' : 'true' );
				$$( 'a, button', slide ).forEach( ( el ) => ( el.tabIndex = visible ? 0 : -1 ) );
			} );
			prev.disabled = index === 0;
			next.disabled = index >= max;
			if ( count ) {
				count.textContent = ( index + 1 ) + ' / ' + ( max + 1 );
			}
		};
		prev.addEventListener( 'click', () => { index -= 1; render(); } );
		next.addEventListener( 'click', () => { index += 1; render(); } );
		carousel.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'ArrowLeft' ) { index -= 1; render(); }
			if ( event.key === 'ArrowRight' ) { index += 1; render(); }
		} );
		let startX = null;
		track.addEventListener( 'touchstart', ( event ) => { startX = event.touches[ 0 ].clientX; }, { passive: true } );
		track.addEventListener( 'touchend', ( event ) => {
			if ( startX === null ) {
				return;
			}
			const dx = event.changedTouches[ 0 ].clientX - startX;
			if ( Math.abs( dx ) > 40 ) {
				index += dx < 0 ? 1 : -1;
				render();
			}
			startX = null;
		} );
		window.addEventListener( 'resize', render );
		render();
	} );

	/* Results gallery: filters, comparison sliders and lightbox */
	$$( '[data-gallery]' ).forEach( ( gallery ) => {
		const buttons = $$( '[data-filter]', gallery );
		buttons.forEach( ( btn ) => btn.addEventListener( 'click', () => {
			const filter = btn.dataset.filter;
			buttons.forEach( ( b ) => {
				const active = b === btn;
				b.classList.toggle( 'is-active', active );
				b.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
			} );
			$$( '.ceori-result', gallery ).forEach( ( item ) => {
				item.hidden = filter !== '*' && ! ( ' ' + item.dataset.cats + ' ' ).includes( ' ' + filter + ' ' );
			} );
		} ) );
	} );

	$$( '[data-compare]' ).forEach( ( compare ) => {
		const range = compare.querySelector( 'input[type="range"]' );
		if ( range ) {
			const set = () => compare.style.setProperty( '--pos', range.value + '%' );
			range.addEventListener( 'input', set );
			set();
		}
	} );

	let lightbox = null;
	let lastFocus = null;
	const closeLightbox = () => {
		if ( lightbox ) {
			lightbox.hidden = true;
			document.body.style.overflow = '';
			if ( lastFocus ) {
				lastFocus.focus();
			}
		}
	};
	const openLightbox = ( src, caption ) => {
		if ( ! lightbox ) {
			lightbox = document.createElement( 'div' );
			lightbox.className = 'ceori-lightbox';
			lightbox.setAttribute( 'role', 'dialog' );
			lightbox.setAttribute( 'aria-modal', 'true' );
			lightbox.innerHTML = '<button type="button" class="ceori-round-btn ceori-lightbox__close" aria-label="' + ( i18n.close || 'Close' ) + '">&times;</button><figure><img alt=""><p></p></figure>';
			document.body.appendChild( lightbox );
			lightbox.addEventListener( 'click', ( event ) => {
				if ( event.target === lightbox || event.target.closest( '.ceori-lightbox__close' ) ) {
					closeLightbox();
				}
			} );
			lightbox.addEventListener( 'keydown', ( event ) => {
				if ( event.key === 'Escape' ) {
					closeLightbox();
				}
				if ( event.key === 'Tab' ) {
					event.preventDefault();
					lightbox.querySelector( 'button' ).focus();
				}
			} );
		}
		lastFocus = document.activeElement;
		lightbox.querySelector( 'img' ).src = src;
		lightbox.querySelector( 'img' ).alt = caption;
		lightbox.querySelector( 'p' ).textContent = caption;
		lightbox.setAttribute( 'aria-label', caption );
		lightbox.hidden = false;
		document.body.style.overflow = 'hidden';
		lightbox.querySelector( 'button' ).focus();
	};
	document.addEventListener( 'click', ( event ) => {
		const trigger = event.target.closest( '[data-lightbox]' );
		if ( trigger ) {
			openLightbox( trigger.dataset.lightbox, trigger.dataset.caption || '' );
		}
	} );

	/* Share: copy link */
	document.addEventListener( 'click', ( event ) => {
		const btn = event.target.closest( '[data-copy]' );
		if ( ! btn || ! navigator.clipboard ) {
			return;
		}
		navigator.clipboard.writeText( btn.dataset.copy ).then( () => {
			btn.classList.add( 'is-copied' );
			btn.setAttribute( 'aria-label', i18n.copied || 'Link copied' );
			window.setTimeout( () => btn.classList.remove( 'is-copied' ), 2000 );
		} );
	} );

	/* Appointment form */
	const phonePattern = /^(?:\+?91|0)?[6-9]\d{9}$|^(?:\+?91|0)[1-9]\d{9}$/;
	const messages = {
		full_name: 'Please enter your full name.',
		phone: 'Please enter a valid Indian mobile or landline number, e.g. +91 98765 43210.',
		email: 'Please enter a valid email address or leave it empty.',
		treatment: 'Please choose a treatment category.',
		consent: 'Please confirm that we may contact you about your request.',
	};

	const setError = ( form, name, message ) => {
		const field = form.querySelector( '[name="' + name + '"]' );
		if ( ! field ) {
			return;
		}
		const wrap = field.closest( '.ceori-field' );
		const error = document.getElementById( field.getAttribute( 'aria-describedby' ) );
		if ( wrap ) {
			wrap.classList.toggle( 'has-error', !! message );
		}
		field.setAttribute( 'aria-invalid', message ? 'true' : 'false' );
		if ( error ) {
			error.textContent = message || '';
		}
	};

	const validate = ( form ) => {
		const errors = {};
		const value = ( name ) => ( form.elements[ name ] ? form.elements[ name ].value.trim() : '' );
		if ( value( 'full_name' ).length < 2 ) {
			errors.full_name = messages.full_name;
		}
		if ( ! phonePattern.test( value( 'phone' ).replace( /[\s\-().]/g, '' ) ) ) {
			errors.phone = messages.phone;
		}
		if ( value( 'email' ) && ! form.elements.email.checkValidity() ) {
			errors.email = messages.email;
		}
		if ( ! value( 'treatment' ) ) {
			errors.treatment = messages.treatment;
		}
		if ( form.elements.consent && ! form.elements.consent.checked ) {
			errors.consent = messages.consent;
		}
		[ 'full_name', 'phone', 'email', 'treatment', 'consent', 'preferred_date' ].forEach( ( name ) => setError( form, name, errors[ name ] ) );
		return errors;
	};

	const refreshNonce = ( form ) => fetch( settings.restUrl.replace( /enquiry\/?$/, 'nonce' ), { credentials: 'same-origin', cache: 'no-store' } )
		.then( ( r ) => r.json() )
		.then( ( data ) => {
			form.elements.ceori_nonce.value = data.nonce;
		} );

	const submitForm = ( form, retried ) => {
		const body = new FormData( form );
		return fetch( settings.restUrl, { method: 'POST', body, credentials: 'same-origin' } )
			.then( ( r ) => r.json().then( ( data ) => ( { status: r.status, data } ) ) )
			.then( ( { data } ) => {
				if ( data && data.code === 'stale_nonce' && ! retried ) {
					return refreshNonce( form ).then( () => submitForm( form, true ) );
				}
				return data;
			} );
	};

	$$( '[data-ceori-form]' ).forEach( ( form ) => {
		if ( ! settings.restUrl ) {
			return;
		}
		const status = form.querySelector( '.ceori-form__status' );
		const button = form.querySelector( 'button[type="submit"]' );
		const label = button ? button.innerHTML : '';

		form.addEventListener( 'submit', ( event ) => {
			event.preventDefault();
			const errors = validate( form );
			if ( Object.keys( errors ).length ) {
				const first = form.querySelector( '[aria-invalid="true"]' );
				if ( first ) {
					first.focus();
				}
				return;
			}
			button.disabled = true;
			button.textContent = i18n.sending || 'Sending…';
			status.className = 'ceori-form__status';
			status.textContent = '';

			submitForm( form, false )
				.then( ( data ) => {
					if ( data && data.ok ) {
						status.classList.add( 'is-success' );
						status.textContent = data.message;
						form.reset();
						if ( data.redirect ) {
							// WhatsApp / email app with the details filled in; a link is kept in case it is blocked.
							const link = document.createElement( 'a' );
							link.href = data.redirect;
							link.className = 'ceori-form__open';
							link.textContent = data.redirect.indexOf( 'wa.me' ) !== -1 ? ( i18n.openWhatsapp || 'Open WhatsApp' ) : ( i18n.openEmail || 'Open email' );
							status.appendChild( document.createTextNode( ' ' ) );
							status.appendChild( link );
							window.setTimeout( () => {
								window.location.href = data.redirect;
							}, 700 );
						}
					} else {
						status.classList.add( 'is-error' );
						status.textContent = ( data && data.message ) || i18n.error;
						if ( data && data.errors ) {
							Object.keys( data.errors ).forEach( ( name ) => setError( form, name, data.errors[ name ] ) );
						}
					}
				} )
				.catch( () => {
					status.classList.add( 'is-error' );
					status.textContent = i18n.error || 'Sorry, something went wrong.';
				} )
				.finally( () => {
					button.disabled = false;
					button.innerHTML = label;
					status.focus();
				} );
		} );

		// Clear a field's error as soon as the visitor fixes it.
		form.addEventListener( 'change', ( event ) => {
			if ( event.target.name && event.target.getAttribute( 'aria-invalid' ) === 'true' ) {
				validate( form );
			}
		} );
	} );

	/* Side panel (header hamburger) */
	$$( '.ceori-offcanvas' ).forEach( ( wrap ) => {
		const toggle = wrap.querySelector( '[data-offcanvas-open]' );
		const panel = wrap.querySelector( '.ceori-offcanvas__panel' );
		const overlay = wrap.querySelector( '.ceori-offcanvas__overlay' );
		if ( ! toggle || ! panel ) {
			return;
		}
		// The sticky header uses backdrop-filter, which would trap fixed children: move the panel to <body>.
		document.body.appendChild( overlay );
		document.body.appendChild( panel );
		const focusables = () => $$( 'a[href], button:not([disabled])', panel );
		const close = () => {
			panel.classList.remove( 'is-open' );
			overlay.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
			document.documentElement.classList.remove( 'has-panel' );
			window.setTimeout( () => {
				panel.hidden = true;
				overlay.hidden = true;
			}, reduceMotion ? 0 : 400 );
			toggle.focus();
		};
		const open = () => {
			panel.hidden = false;
			overlay.hidden = false;
			window.requestAnimationFrame( () => {
				panel.classList.add( 'is-open' );
				overlay.classList.add( 'is-open' );
			} );
			toggle.setAttribute( 'aria-expanded', 'true' );
			document.documentElement.classList.add( 'has-panel' );
			const first = panel.querySelector( '.ceori-offcanvas__close' );
			if ( first ) {
				first.focus();
			}
		};
		toggle.addEventListener( 'click', open );
		// Submenu toggles: visitors do not load the Navigation block's own script (inc/performance.php).
		if ( ! document.body.classList.contains( 'logged-in' ) ) {
			$$( '.wp-block-navigation-submenu__toggle', panel ).forEach( ( button ) => {
				button.addEventListener( 'click', () => {
					button.setAttribute( 'aria-expanded', 'true' === button.getAttribute( 'aria-expanded' ) ? 'false' : 'true' );
				} );
			} );
		}
		if ( window.location.hash === '#clinic-panel' ) {
			open();
		}
		[ overlay, panel.querySelector( '.ceori-offcanvas__close' ) ].forEach( ( el ) => el.addEventListener( 'click', close ) );
		panel.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'Escape' ) {
				close();
			}
			if ( event.key === 'Tab' ) {
				const items = focusables();
				const first = items[ 0 ];
				const last = items[ items.length - 1 ];
				if ( event.shiftKey && document.activeElement === first ) {
					event.preventDefault();
					last.focus();
				} else if ( ! event.shiftKey && document.activeElement === last ) {
					event.preventDefault();
					first.focus();
				}
			}
		} );
	} );

	/* Category strip: shared background follows the hovered / focused category */
	$$( '[data-panels]' ).forEach( ( strip ) => {
		const panels = $$( '.ceori-panel', strip );
		const images = $$( '.ceori-panels__img', strip );
		const activate = ( index ) => {
			panels.forEach( ( p ) => p.classList.toggle( 'is-active', p.dataset.panel === index ) );
			images.forEach( ( img ) => img.classList.toggle( 'is-active', img.dataset.panel === index ) );
		};
		panels.forEach( ( panel ) => {
			panel.addEventListener( 'mouseenter', () => activate( panel.dataset.panel ) );
			panel.addEventListener( 'focusin', () => activate( panel.dataset.panel ) );
		} );
	} );

	/* Video play button: opens the video in a lightbox (falls back to the YouTube link) */
	document.addEventListener( 'click', ( event ) => {
		const play = event.target.closest( '[data-video]' );
		if ( ! play ) {
			return;
		}
		event.preventDefault();
		const id = encodeURIComponent( play.dataset.video );
		const title = ( play.dataset.title || play.textContent ).trim().replace( /[<>"]/g, '' );
		// YouTube (wide, or tall for Shorts) or an Instagram reel (tall).
		const insta = 'instagram' === play.dataset.provider;
		const src = insta
			? 'https://www.instagram.com/reel/' + id + '/embed/'
			: 'https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&rel=0';
		const tall = insta || play.closest( '.ceori-vcard' );
		const box = document.createElement( 'div' );
		box.className = 'ceori-lightbox ceori-lightbox--video' + ( tall ? ' ceori-lightbox--tall' : '' );
		box.setAttribute( 'role', 'dialog' );
		box.setAttribute( 'aria-modal', 'true' );
		box.setAttribute( 'aria-label', title );
		box.innerHTML = '<button type="button" class="ceori-round-btn ceori-lightbox__close" aria-label="' + ( i18n.close || 'Close' ) + '">&times;</button><div class="ceori-video' + ( tall ? ' ceori-video--tall' : '' ) + '"><iframe src="' + src + '" title="' + title + '" allow="autoplay; encrypted-media; picture-in-picture; clipboard-write" allowfullscreen></iframe></div>';
		document.body.appendChild( box );
		document.body.style.overflow = 'hidden';
		const remove = () => {
			box.remove();
			document.body.style.overflow = '';
			play.focus();
		};
		box.addEventListener( 'click', ( e ) => {
			if ( e.target === box || e.target.closest( '.ceori-lightbox__close' ) ) {
				remove();
			}
		} );
		box.addEventListener( 'keydown', ( e ) => {
			if ( e.key === 'Escape' ) {
				remove();
			}
		} );
		box.querySelector( 'button' ).focus();
	} );

	/* Search suggestions: matching treatments, specialists and articles while typing
	   (arrow keys + Enter, or click). Enter on the text itself opens the full results page. */
	if ( settings.searchUrl ) {
		$$( 'form.ceori-search' ).forEach( ( form, n ) => {
			const input = form.querySelector( 'input[name="s"]' );
			const wrap = form.querySelector( '.wp-block-search__inside-wrapper' ) || form;
			if ( ! input ) {
				return;
			}
			const list = document.createElement( 'ul' );
			list.className = 'ceori-suggest';
			list.id = 'ceori-suggest-' + n;
			list.setAttribute( 'role', 'listbox' );
			list.hidden = true;
			wrap.appendChild( list );
			input.setAttribute( 'role', 'combobox' );
			input.setAttribute( 'aria-autocomplete', 'list' );
			input.setAttribute( 'aria-controls', list.id );
			input.setAttribute( 'aria-expanded', 'false' );
			input.setAttribute( 'autocomplete', 'off' );

			let timer = 0;
			let active = -1;
			let last = '';
			const cache = {};
			const items = () => $$( '[role="option"]', list );
			const close = () => {
				list.hidden = true;
				input.setAttribute( 'aria-expanded', 'false' );
				input.removeAttribute( 'aria-activedescendant' );
				active = -1;
			};
			const highlight = ( i ) => {
				const opts = items();
				opts.forEach( ( o, j ) => o.setAttribute( 'aria-selected', j === i ? 'true' : 'false' ) );
				active = i;
				if ( opts[ i ] ) {
					input.setAttribute( 'aria-activedescendant', opts[ i ].id );
				}
			};
			const esc = ( s ) => String( s ).replace( /[&<>"]/g, ( c ) => ( { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[ c ] ) );
			const show = ( q, results ) => {
				if ( q !== input.value.trim() ) {
					return;
				}
				const all = form.action + ( form.action.indexOf( '?' ) > -1 ? '&' : '?' ) + 's=' + encodeURIComponent( q );
				list.innerHTML = results.map( ( r, i ) => '<li role="option" id="' + list.id + '-' + i + '" aria-selected="false"><a href="' + esc( r.url ) + '" tabindex="-1">'
					+ ( r.image ? '<img src="' + esc( r.image ) + '" alt="" width="44" height="44" loading="lazy">' : '<span class="ceori-suggest__ph" aria-hidden="true"></span>' )
					+ '<span class="ceori-suggest__text"><span class="ceori-suggest__type">' + esc( r.type ) + '</span><span class="ceori-suggest__title">' + esc( r.title ) + '</span></span></a></li>' ).join( '' )
					+ ( results.length
						? '<li role="option" id="' + list.id + '-all" aria-selected="false" class="ceori-suggest__all"><a href="' + esc( all ) + '" tabindex="-1">' + esc( ( i18n.allResults || 'See all results for “%s”' ).replace( '%s', q ) ) + '</a></li>'
						: '<li class="ceori-suggest__none">' + esc( ( i18n.noResults || 'No matches for “%s”. Try another word.' ).replace( '%s', q ) ) + '</li>' );
				list.hidden = false;
				input.setAttribute( 'aria-expanded', 'true' );
				active = -1;
			};
			input.addEventListener( 'input', () => {
				const q = input.value.trim();
				window.clearTimeout( timer );
				if ( q.length < 2 ) {
					close();
					return;
				}
				timer = window.setTimeout( () => {
					if ( cache[ q ] ) {
						show( q, cache[ q ] );
						return;
					}
					last = q;
					window.fetch( settings.searchUrl + ( settings.searchUrl.indexOf( '?' ) > -1 ? '&' : '?' ) + 'q=' + encodeURIComponent( q ) )
						.then( ( r ) => ( r.ok ? r.json() : [] ) )
						.then( ( data ) => {
							cache[ q ] = Array.isArray( data ) ? data : [];
							if ( q === last ) {
								show( q, cache[ q ] );
							}
						} )
						.catch( close );
				}, 200 );
			} );
			input.addEventListener( 'keydown', ( e ) => {
				const opts = items();
				if ( list.hidden || ! opts.length ) {
					return;
				}
				if ( e.key === 'ArrowDown' || e.key === 'ArrowUp' ) {
					e.preventDefault();
					// Down from the text box goes to the first suggestion, past the last back to the text box.
					let next = active + ( e.key === 'ArrowDown' ? 1 : -1 );
					if ( next < -1 ) {
						next = opts.length - 1;
					} else if ( next >= opts.length ) {
						next = -1;
					}
					highlight( next );
					if ( next === -1 ) {
						input.removeAttribute( 'aria-activedescendant' );
					}
				} else if ( e.key === 'Enter' && active > -1 && opts[ active ] ) {
					e.preventDefault();
					window.location.href = opts[ active ].querySelector( 'a' ).href;
				} else if ( e.key === 'Escape' ) {
					close();
				}
			} );
			input.addEventListener( 'focus', () => {
				if ( list.children.length && input.value.trim().length >= 2 ) {
					list.hidden = false;
					input.setAttribute( 'aria-expanded', 'true' );
				}
			} );
			document.addEventListener( 'click', ( e ) => {
				if ( ! form.contains( e.target ) ) {
					close();
				}
			} );
		} );
	}

	/* FAQ accordions (Elementor Accordion with the ceori-n-faq class): one question open at a time.
	   Elementor's own accordion script is not loaded for visitors, so the browser's native
	   exclusive <details> group (same "name") does it, with a fallback for older browsers. */
	$$( '.ceori-n-faq .e-n-accordion' ).forEach( ( accordion, n ) => {
		const items = $$( ':scope > details', accordion );
		items.forEach( ( item ) => {
			item.setAttribute( 'name', 'ceori-faq-' + n );
			item.addEventListener( 'toggle', () => {
				if ( item.open ) {
					items.forEach( ( other ) => {
						if ( other !== item && other.open ) {
							other.open = false;
						}
					} );
				}
			} );
		} );
	} );
}() );
