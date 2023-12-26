'use strict';
var isDebugEnv = false;
const iStatus = {
		success: 100,
		invalid: -50,
		unsupported: -100,
		error: -200
	},
	AGR_IFIELD_NAME = "agreement",
	APButtonColor = {
		black: "black",
		white: "white",
		whiteOutline: "white-outline"
	},
	APButtonType = {
		buy: "buy",
		pay: "pay",
		plain: "plain",
		order: "order",
		donate: "donate",
		continue: "continue",
		checkout: "check-out"
	},
	APRequiredFeatures = {
		address_validation: "address_validation",
		support_recurring: "support_recurring",
		support_subscription: "support_subscription"
	},
	APErrorCode = {
		shippingContactInvalid: "shippingContactInvalid",
		billingContactInvalid: "billingContactInvalid",
		addressUnserviceable: "addressUnserviceable",
		couponCodeInvalid: "couponCodeInvalid",
		couponCodeExpired: "couponCodeExpired",
		unknown: "unknown"
	},
	APErrorContactField = {
		phoneNumber: "phoneNumber",
		emailAddress: "emailAddress",
		name: "name",
		phoneticName: "phoneticName",
		postalAddress: "postalAddress",
		addressLines: "addressLines",
		locality: "locality",
		subLocality: "subLocality",
		postalCode: "postalCode",
		administrativeArea: "administrativeArea",
		subAdministrativeArea: "subAdministrativeArea",
		country: "country",
		countryCode: "countryCode"
	},
	GP_IFIELD_NAME = "igp",
	GPEnvironment = {
		test: "TEST",
		production: "PRODUCTION"
	},
	GPButtonColor = {
		default: "default",
		black: "black",
		white: "white"
	},
	GPButtonType = {
		buy: "buy",
		donate: "donate",
		plain: "plain",
		pay: "pay",
		book: "book",
		checkout: "checkout",
		order: "order",
		subscribe: "subscribe"
	},
	GPButtonSizeMode = {
		static: "static",
		fill: "fill"
	},
	GPBillingAddressFormat = {
		min: "MIN",
		full: "FULL"
	};

function setDebugEnv(a)
{
	isDebugEnv = isDebugEnv || a
}

function logDebug(a, b = false)
{
	a && (b || isDebugEnv || loggingEnabled) && (typeof a === "string" ? console.log(JSON.stringify(a)) : typeof a === "object" && (a.label && a.data ? console.log(a.label, JSON.stringify(a.data)) : console.log(JSON.stringify(a.label || a.data))))
}

function logError(a, b)
{
	const c = [];
	a && c.push(a);
	b && (typeof b === "string" ? c.push(b) : typeof b.message === "string" ? c.push(b.message) : c.push(JSON.stringify(b)));
	console.error.apply(console.error, c)
}

function parseQueryString(a)
{
	let b = {};
	a.replace(/([^?=&]+)(=([^&]*))?/g, function (a, d, e, f)
	{
		b[d] = f
	});
	return b
}

function sendCallback(a, b, c)
{
	a = Object.assign(
	{},
	{
		action: "callback",
		tokenType: a
	},
	{
		clbParams: b
	});
	c = c && c.contentWindow || window.parent;
	c.postMessage(a, "*")
}

function sendError(a, b)
{
	window.parent.postMessage(
	{
		action: "error",
		tokenType: a,
		error: b
	}, "*")
}
async function execCallback(a)
{
	if (!a) throw "execCallback: Invalid callback params";
	if (typeof a === "string") try
	{
		a = JSON.parse(a)
	}
	catch (e)
	{
		logError("Failed to resolve callback params object", e);
		throw "execCallback: Failed to resolve callback params object";
	}
	if (!a) throw "execCallback: Callback params object is empty";
	if (!a.callbackName) throw "execCallback: Invalid callback name";
	var b = a.callbackName.split(".");
	const c = b.pop();
	(b = b.join(".")) || (b = "window");
	b = eval(b);
	let d = a.callbackArgs;
	d && (Array.isArray(d) ||
		(d = [d]));
	if (a.callbackType === "promise" || a.promise) try
	{
		let e = await b[c].apply(b, d);
		return a.promise && a.promise.resolve ? await execCallback(
		{
			callbackName: a.promise.resolve,
			callbackArgs: e
		}) :
		{
			response: e
		}
	}
	catch (e)
	{
		logError("execCallback", e);
		return a.promise && a.promise.reject ? await execCallback(
		{
			callbackName: a.promise.reject,
			callbackArgs: e ? e.message || e : "No error description available"
		}) :
		{
			error: exMsg(e, "No error description available")
		}
	}
	else try
	{
		return {
			response: b[c].apply(b, d)
		}
	}
	catch (e)
	{
		logError("execCallback",
			e);
		return {
			error: exMsg(e, "No error description available")
		}
	}
}

function execFunction(a, b)
{
	var c = a.split(".");
	a = c.pop();
	c = (c = c.join(".")) ? eval(c) : window;
	return c[a].apply(c, b)
}
async function handleCallback(a, b)
{
	if (!a.clbParams || !a.clbParams.client) throw "Invalid Callback - parameters are missing";
	const c = await execCallback(a.clbParams.client);
	if (a.clbParams.server && b)
	{
		a = a.clbParams.server;
		let d = [];
		if (a.callbackArgs) Array.isArray(a.callbackArgs) ? d = a.callbackArgs : d.push(a.callbackArgs);
		d.push(c.response || c.error);
		b.postMessage(
		{
			action: "callback-result",
			clbParams:
			{
				callbackName: a.promise ? c.error ? a.promise.reject : a.promise.resolve : a.callbackName,
				callbackArgs: d
			}
		}, "*")
	}
	else if (c &&
		c.error)
	{
		if (typeof c.error === "string") throw c.error;
		if (typeof c.error.message === "string") throw c.error.message;
		logError("Callback error", c.error);
		throw "Callback error occured, check the logs";
	}
}

function roundTo(a, b)
{
	return Number(a).toFixed(b)
}

function roundToNumber(a, b)
{
	return Number(roundTo(a, b))
}

function isDefined(a)
{
	return typeof a !== "undefined" && a !== null
}

function chained(a)
{
	if (!a || !Array.isArray(a)) throw "chained function. Argument must be an array";
	let b = a.length > 0 && a[0];
	if (!b) return false;
	if (typeof b === "string")
	{
		b = window[b];
		if (!b) return false
	}
	for (let c = 1; c < a.length; c++)
	{
		b = b[a[c]];
		if (!b) return false
	}
	return true
}

function exMsg(a, b = null)
{
	return a ? a.message || JSON.stringify(a) : b || JSON.stringify(a)
}

function logAndShow(a)
{
	if (a)
	{
		console.log(a.title || "logAndShow", a.log || a);
		a.show && alert(a.show)
	}
	else alert("logAndShow parameter is null")
};
const CARD_NUMBER_IFIELD_NAME = "card-number",
	CVV_IFIELD_NAME = "cvv",
	CARD_NUMBER_TOKEN_NAME = "card",
	CVV_TOKEN_NAME = "cvv",
	ACH_IFIELD_NAME = "ach",
	ACH_TOKEN_NAME = "ach",
	ERROR_FIELD_NAME = "card-data-error",
	THREEDS_JS_URL = "https://cdn.cardknox.com/sdk-3ds/1.0.2203.2501-beta/cardknox-3ds.min.js";
var loggingEnabled = false,
	cardFrameLoaded = false,
	cvvFrameLoaded = false,
	achFrameLoaded = false,
	cardTokenRecieved = false,
	cvvTokenRecieved = false,
	achTokenRecieved = false,
	tokensReceived = false,
	latestErrorTime = new Date,
	cachedIFieldStyles = {},
	cachedAccountxKey = "",
	cachedAccountSoftwareName = "",
	cachedAccountSoftwareVersion = "",
	ifieldEventCallbacks = {},
	autoFormat = false,
	autoFormatSeparator = " ",
	ifieldDataCache = {
		cardNumberIsValid: false,
		cardNumberLength: 0,
		cardNumberFormattedLength: 0,
		cardNumberIsEmpty: true,
		issuer: "unknown",
		cvvIsValid: false,
		cvvLength: 0,
		cvvIsEmpty: true,
		achLength: 0,
		achIsEmpty: true,
		achIsValid: false,
		lastIfieldChanged: ""
	};
let amountEvents = ["change", "blur", "keypress"];

function getByCustomAttribute(a, b)
{
	return document.querySelector("[" + a + "='" + b + "']")
}

function pingIfields()
{
	for (var a = document.getElementsByTagName("iframe"), b = 0; b < a.length; b++) /(ifield|igp|iap|agreement).htm/.test(a[b].src) && a[b].contentWindow.postMessage(
	{
		action: "ping"
	}, "*")
}
window.addEventListener("message", async function (a)
{
	try
	{
		var b = a.data;
		if (b.action === "loaded")
		{
			log("Message received: ifield loaded");
			for (var c, d, e = document.getElementsByTagName("iframe"), f = 0; f < e.length; f++)
				if (e[f].contentWindow == a.source)
				{
					c = e[f].getAttribute("data-ifields-id");
					d = e[f].getAttribute("data-ifields-placeholder");
					break
				} log("Loaded ifield id: " + c);
			for (var g in cachedIFieldStyles) setIfieldStyle(g, cachedIFieldStyles[g]);
			setAccount(cachedAccountxKey, cachedAccountSoftwareName, cachedAccountSoftwareVersion);
			if (c === CARD_NUMBER_IFIELD_NAME)
			{
				initDataField(a, c, CARD_NUMBER_TOKEN_NAME, d);
				cardFrameLoaded = true;
				autoFormat && enableAutoFormatting(autoFormatSeparator)
			}
			else if (c === CVV_IFIELD_NAME)
			{
				initDataField(a, c, CVV_TOKEN_NAME, d);
				cvvFrameLoaded = true
			}
			else if (c === ACH_IFIELD_NAME)
			{
				initDataField(a, c, ACH_TOKEN_NAME, d);
				achFrameLoaded = true
			}
			else if (c === GP_IFIELD_NAME)
			{
				ckGooglePay.isFrameLoaded = true;
				ckGooglePay.isEnabled && !ckGooglePay.isButtonLoaded && ckGooglePay.load.call(ckGooglePay)
			}
			else if (c === AGR_IFIELD_NAME)
			{
				ckCustomerAgreement.isFrameLoaded =
					true;
				ckCustomerAgreement.isAgreementLoaded || ckCustomerAgreement.loadAgreement.call(ckCustomerAgreement)
			}
		}
		else if (b.action === "token")
		{
			log("Message received: token");
			if (b.data.result === "error")
			{
				latestErrorTime = new Date;
				getByCustomAttribute("data-ifields-id", "card-data-error").innerHTML = b.data.errorMessage
			}
			else if (b.data.xTokenType === CARD_NUMBER_TOKEN_NAME)
			{
				getByCustomAttribute("data-ifields-id", "card-number-token").value = b.data.xToken;
				b.data.threeDSJwt && window.ck3DS && ck3DS.setJwt(b.data.threeDSJwt);
				cardTokenRecieved = true
			}
			else if (b.data.xTokenType === CVV_TOKEN_NAME)
			{
				getByCustomAttribute("data-ifields-id", "cvv-token").value = b.data.xToken;
				cvvTokenRecieved = true
			}
			else if (b.data.xTokenType === ACH_TOKEN_NAME)
			{
				getByCustomAttribute("data-ifields-id", "ach-token").value = b.data.xToken;
				achTokenRecieved = true
			}
		}
		else if (b.action === "autoSubmit")
		{
			log("auto submitting form with id " + b.data.formId);
			document.getElementById(b.data.formId).dispatchEvent(new Event("submit",
			{
				bubbles: true,
				cancelable: true
			}))
		}
		else if (b.action ===
			"update")
		{
			log("Message recieved: update");
			if (b.xTokenType === CARD_NUMBER_TOKEN_NAME)
			{
				if (b.data.ifieldValueChanged && iFieldElementExists(CVV_IFIELD_NAME))
				{
					var m = {
						action: "updateIssuer",
						issuer: b.data.issuer
					};
					getByCustomAttribute("data-ifields-id", CVV_IFIELD_NAME).contentWindow.postMessage(m, "*")
				}
				ifieldDataCache.cardNumberIsValid = b.data.isValid;
				ifieldDataCache.cardNumberLength = b.data.cardNumberLength;
				ifieldDataCache.cardNumberFormattedLength = b.data.length;
				ifieldDataCache.cardNumberIsEmpty = b.data.isEmpty;
				ifieldDataCache.issuer = b.data.issuer;
				ifieldDataCache.triggeredByIfield = CARD_NUMBER_IFIELD_NAME;
				if (b.data.ifieldValueChanged) ifieldDataCache.lastIfieldChanged = CARD_NUMBER_IFIELD_NAME
			}
			else if (b.xTokenType === CVV_TOKEN_NAME)
			{
				ifieldDataCache.cvvIsValid = b.data.isValid;
				ifieldDataCache.cvvLength = b.data.length;
				ifieldDataCache.cvvIsEmpty = b.data.isEmpty;
				ifieldDataCache.triggeredByIfield = CVV_IFIELD_NAME;
				if (b.data.ifieldValueChanged) ifieldDataCache.lastIfieldChanged = CVV_IFIELD_NAME
			}
			else if (b.xTokenType === ACH_TOKEN_NAME)
			{
				ifieldDataCache.achLength =
					b.data.length;
				ifieldDataCache.achIsEmpty = b.data.isEmpty;
				ifieldDataCache.achIsValid = b.data.isValid;
				ifieldDataCache.triggeredByIfield = ACH_IFIELD_NAME;
				if (b.data.ifieldValueChanged) ifieldDataCache.lastIfieldChanged = ACH_IFIELD_NAME
			}
			ifieldDataCache.ifieldValueChanged = b.data.ifieldValueChanged;
			var h = ifieldEventCallbacks[b.data.event];
			if (h)
				for (f = 0; f < h.length; f++)
					if (typeof h[f] === "function") h[f](Object.assign(
					{
						event: b.data.event
					}, ifieldDataCache))
		}
		else if (b.action === "callback")
		{
			c = null;
			if (b.tokenType && b.clbParams.server)
				if (c =
					getIfieldWnd(b.tokenType)) c = c.contentWindow;
			handleCallback(b, c)
		}
		else if (b.action === "callback-result")
		{
			log("ifields - message received: callback-result (for " + JSON.stringify(b.data) + ")");
			b.clbParams && execCallback(b.clbParams)
		}
	}
	catch (l)
	{
		console.error("Error message event listener for " + JSON.stringify(a), JSON.stringify(l))
	}
}, false);
pingIfields();

function initDataField(a, b, c, d)
{
	a.source.postMessage(
	{
		action: "init",
		tokenType: c,
		referrer: window.location.toString()
	}, "*");
	setPlaceholder(b, d)
}

function log(a)
{
	loggingEnabled && console.log(a)
}

function iFieldElementExists(a)
{
	a = getByCustomAttribute("data-ifields-id", a);
	return typeof a !== "undefined" && a !== null
}

function elementExists(a)
{
	a = document.getElementById(a);
	return typeof a !== "undefined" && a !== null
}

function getIfieldWnd(a)
{
	let b = null;
	switch (a)
	{
	case GP_IFIELD_NAME:
		b = ckGooglePay.iframeField;
		break;
	case AGR_IFIELD_NAME:
		b = ckCustomerAgreement.iframeField
	}
	b && b.contentWindow || (b = getByCustomAttribute("data-ifields-id", a));
	return b
}

function getTokens(a, b, c)
{
	c = c || 6E4;
	a = a || Function.prototype;
	b = b || Function.prototype;
	achTokenRecieved = cvvTokenRecieved = cardTokenRecieved = false;
	if (iFieldElementExists(CARD_NUMBER_IFIELD_NAME)) getByCustomAttribute("data-ifields-id", "card-number-token").value = "";
	if (iFieldElementExists(CVV_IFIELD_NAME)) getByCustomAttribute("data-ifields-id", "cvv-token").value = "";
	if (iFieldElementExists(ACH_IFIELD_NAME)) getByCustomAttribute("data-ifields-id", "ach-token").value = "";
	setError("");
	tokensReceived = false;
	var d = {
		action: "getToken"
	};
	if (iFieldElementExists(CARD_NUMBER_IFIELD_NAME)) var e = setInterval(function ()
	{
		if (cardFrameLoaded)
		{
			clearInterval(e);
			getByCustomAttribute("data-ifields-id", CARD_NUMBER_IFIELD_NAME).contentWindow.postMessage(d, "*")
		}
	}, 100);
	if (iFieldElementExists(CVV_IFIELD_NAME)) var f = setInterval(function ()
	{
		if (cvvFrameLoaded)
		{
			clearInterval(f);
			getByCustomAttribute("data-ifields-id", CVV_IFIELD_NAME).contentWindow.postMessage(d, "*")
		}
	}, 100);
	if (iFieldElementExists(ACH_IFIELD_NAME)) var g = setInterval(function ()
	{
		if (achFrameLoaded)
		{
			clearInterval(g);
			getByCustomAttribute("data-ifields-id", ACH_IFIELD_NAME).contentWindow.postMessage(d, "*")
		}
	}, 100);
	var m = iFieldElementExists(CARD_NUMBER_IFIELD_NAME),
		h = iFieldElementExists(CVV_IFIELD_NAME),
		l = iFieldElementExists(ACH_IFIELD_NAME),
		k = setInterval(function ()
		{
			if ((m == false || cardTokenRecieved) && (h == false || cvvTokenRecieved) && (l == false || achTokenRecieved))
			{
				clearInterval(k);
				tokensReceived = true;
				setError("");
				a()
			}
		}, 100),
		n = (new Date).getTime(),
		p = setInterval(function ()
		{
			var a = (new Date).getTime();
			if (n < latestErrorTime)
			{
				clearInterval(e);
				clearInterval(f);
				clearInterval(g);
				clearInterval(p);
				clearInterval(k);
				b()
			}
			else if (tokensReceived) clearInterval(p);
			else if (a - n > c)
			{
				clearInterval(e);
				clearInterval(f);
				clearInterval(g);
				clearInterval(p);
				clearInterval(k);
				getByCustomAttribute("data-ifields-id", "card-data-error").innerHTML = "Transaction timed out";
				b()
			}
		}, 100)
}

function clearIfield(a)
{
	log("Clearing ifield data for iframe id " + a);
	getByCustomAttribute("data-ifields-id", a).contentWindow.postMessage(
	{
		action: "clearData"
	}, "*")
}

function setIfieldStyle(a, b)
{
	cachedIFieldStyles[a] = b;
	let c = 0;
	var d = setInterval(function ()
	{
		if (iFieldElementExists(a) && (a === CARD_NUMBER_IFIELD_NAME && cardFrameLoaded || a === CVV_IFIELD_NAME && cvvFrameLoaded || a === ACH_IFIELD_NAME && achFrameLoaded))
		{
			clearInterval(d);
			var e = {
				action: "style",
				data: b
			};
			log("Setting ifield style for iframe id " + a);
			getByCustomAttribute("data-ifields-id", a).contentWindow.postMessage(e, "*")
		}
		c += 1;
		if (c >= 50)
		{
			log("Setting ifield style for iframe id timed out");
			clearInterval(d)
		}
	}, 100)
}

function setError(a)
{
	if (iFieldElementExists(ERROR_FIELD_NAME)) getByCustomAttribute("data-ifields-id", ERROR_FIELD_NAME).innerHTML = a
}

function setPlaceholder(a, b)
{
	b = {
		action: "setPlaceholder",
		data: b
	};
	log("Setting ifield placeholder for iframe id " + a);
	getByCustomAttribute("data-ifields-id", a).contentWindow.postMessage(b, "*")
}

function setAccount(a, b, c)
{
	cachedAccountxKey = a;
	cachedAccountSoftwareName = b;
	cachedAccountSoftwareVersion = c;
	var d = {
		action: "setAccountData",
		data:
		{
			xKey: a,
			xSoftwareName: b,
			xSoftwareVersion: c
		}
	};
	if (iFieldElementExists(CARD_NUMBER_IFIELD_NAME)) var e = setInterval(function ()
	{
		if (cardFrameLoaded)
		{
			clearInterval(e);
			log("Setting ifield account data for iframe id " + CARD_NUMBER_IFIELD_NAME);
			getByCustomAttribute("data-ifields-id", CARD_NUMBER_IFIELD_NAME).contentWindow.postMessage(d, "*")
		}
	}, 100);
	if (iFieldElementExists(CVV_IFIELD_NAME)) var f =
		setInterval(function ()
		{
			if (cvvFrameLoaded)
			{
				clearInterval(f);
				log("Setting ifield account data for iframe id cvv");
				getByCustomAttribute("data-ifields-id", CVV_IFIELD_NAME).contentWindow.postMessage(d, "*")
			}
		}, 100);
	if (iFieldElementExists(ACH_IFIELD_NAME)) var g = setInterval(function ()
	{
		if (achFrameLoaded)
		{
			clearInterval(g);
			log("Setting ifield account data for iframe id cvv");
			getByCustomAttribute("data-ifields-id", ACH_IFIELD_NAME).contentWindow.postMessage(d, "*")
		}
	}, 100)
}

function addIfieldKeyPressCallback(a)
{
	addIfieldCallback("input", a)
}

function addIfieldCallback(a, b)
{
	ifieldEventCallbacks[a] ? ifieldEventCallbacks[a].push(b) : ifieldEventCallbacks[a] = [b]
}

function focusIfield(a)
{
	var b = {
		action: "focus"
	};
	iFieldElementExists(a) && getByCustomAttribute("data-ifields-id", a).contentWindow.postMessage(b, "*")
}

function enableLogging()
{
	loggingEnabled = true;
	var a = {
		action: "enableLogging"
	};
	if (iFieldElementExists(CARD_NUMBER_IFIELD_NAME))
	{
		let b = setInterval(function ()
		{
			if (cardFrameLoaded)
			{
				clearInterval(b);
				getByCustomAttribute("data-ifields-id", CARD_NUMBER_IFIELD_NAME).contentWindow.postMessage(a, "*")
			}
		}, 100)
	}
	if (iFieldElementExists(CVV_IFIELD_NAME)) var b = setInterval(function ()
	{
		if (cvvFrameLoaded)
		{
			clearInterval(b);
			log("Setting ifield account data for iframe id cvv");
			getByCustomAttribute("data-ifields-id", CVV_IFIELD_NAME).contentWindow.postMessage(a,
				"*")
		}
	}, 100);
	if (iFieldElementExists(ACH_IFIELD_NAME)) var c = setInterval(function ()
	{
		if (achFrameLoaded)
		{
			clearInterval(c);
			log("Setting ifield account data for iframe id ach");
			getByCustomAttribute("data-ifields-id", ACH_IFIELD_NAME).contentWindow.postMessage(a, "*")
		}
	}, 100)
}

function enableAutoSubmit(a)
{
	if (elementExists(a) === false) throw "Invalid formId (" + a + ")";
	var b = {
		action: "enableAutoSubmit",
		data:
		{
			formId: a
		}
	};
	if (iFieldElementExists(CARD_NUMBER_IFIELD_NAME)) var c = setInterval(function ()
	{
		if (cardFrameLoaded)
		{
			clearInterval(c);
			getByCustomAttribute("data-ifields-id", CARD_NUMBER_IFIELD_NAME).contentWindow.postMessage(b, "*")
		}
	}, 100);
	if (iFieldElementExists(CVV_IFIELD_NAME)) var d = setInterval(function ()
	{
		if (cvvFrameLoaded)
		{
			clearInterval(d);
			getByCustomAttribute("data-ifields-id", CVV_IFIELD_NAME).contentWindow.postMessage(b,
				"*")
		}
	}, 100);
	if (iFieldElementExists(ACH_IFIELD_NAME)) var e = setInterval(function ()
	{
		if (achFrameLoaded)
		{
			clearInterval(e);
			getByCustomAttribute("data-ifields-id", ACH_IFIELD_NAME).contentWindow.postMessage(b, "*")
		}
	}, 100)
}

function enableAutoFormatting(a)
{
	a = a || " ";
	autoFormat = true;
	autoFormatSeparator = a;
	let b = {
		action: "format",
		data:
		{
			formatChar: a
		}
	};
	if (iFieldElementExists(CARD_NUMBER_IFIELD_NAME))
	{
		let a = setInterval(function ()
		{
			if (cardFrameLoaded)
			{
				clearInterval(a);
				getByCustomAttribute("data-ifields-id", CARD_NUMBER_IFIELD_NAME).contentWindow.postMessage(b, "*")
			}
		}, 100)
	}
}

function loadScriptAsync(a, b)
{
	return new Promise((c, d) =>
	{
		if (typeof b !== "undefined") c();
		else
		{
			d = document.createElement("script");
			d.src = a;
			d.async = true;
			d.onload = () =>
			{
				c()
			};
			var e = document.getElementsByTagName("script")[0];
			e.parentNode.insertBefore(d, e)
		}
	})
}

function disable3DS()
{
	let a = {
		action: "disable3DS",
		data:
		{}
	};
	if (iFieldElementExists(CARD_NUMBER_IFIELD_NAME))
	{
		let b = setInterval(function ()
		{
			if (cardFrameLoaded)
			{
				clearInterval(b);
				getByCustomAttribute("data-ifields-id", CARD_NUMBER_IFIELD_NAME).contentWindow.postMessage(a, "*")
			}
		}, 100)
	}
}

function enable3DS(a, b)
{
	b && loadScriptAsync(THREEDS_JS_URL, window.ck3DS).then(() =>
	{
		ck3DS.configuration.onVerifyComplete = b;
		ck3DS.configuration.enableConsoleLogging = loggingEnabled;
		ck3DS.initialized || ck3DS.initialize3DS(a)
	});
	let c = {
		action: "enable3DS",
		data:
		{
			environment: a,
			verificationEnabled: !!b
		}
	};
	if (iFieldElementExists(CARD_NUMBER_IFIELD_NAME))
	{
		let a = setInterval(function ()
			{
				if (cardFrameLoaded)
				{
					clearInterval(a);
					getByCustomAttribute("data-ifields-id", CARD_NUMBER_IFIELD_NAME).contentWindow.postMessage(c, "*")
				}
			},
			100)
	}
}

function verify3DS(a)
{
	typeof window.ck3DS !== "undefined" ? ck3DS.verifyTrans(a) : log("verify3DS called without using enable3DS first to attach a handler!")
}

function enableGooglePay(a)
{
	ckGooglePay.enableGooglePay.call(ckGooglePay, a)
}
window.ckGooglePay = {
	isFrameLoaded: false,
	isEnabled: false,
	environment: "",
	onGetTransactionInfo: "",
	enableGooglePay: function (a)
	{
		this.isDebug = isDebugEnv || a && a.isDebug;
		this.setIframeField(a && a.iframeField);
		this.isFrameLoaded = a && a.isFrameLoaded;
		let b = a && a.amountField;
		typeof b === "string" && (b = document.getElementById(b));
		if (b)
		{
			const a = function ()
			{
				return parseFloat(b.value || "0")
			};
			let d = a();
			const e = function (b)
			{
				b = a();
				if (b !== d)
				{
					ckGooglePay.updateAmount();
					d = b
				}
			};
			amountEvents.forEach((a) =>
			{
				b.addEventListener(a, e)
			})
		}
		this.isEnabled =
			true;
		this.isButtonLoaded = false;
		this.isFrameLoaded ? this.load() : this.timeoutId = setTimeout(() =>
		{
			pingIfields()
		}, 500)
	},
	updateAmount: function (a)
	{
		const b = ckGooglePay;
		if (!b.iframeField)
		{
			if (!a || !a.iframeField) throw "Failed to update Google Pay Amount. iframe with Google Pay button is not provided";
			b.setIframeField(a.iframeField)
		}
		execCallback(
		{
			callbackName: b.onGetTransactionInfo
		}).then((a) =>
		{
			if (!a) throw "Failed to update Google Pay Amount. Please try again";
			if (a.error)
			{
				console.error("updateGooglePayAmount. Error getting transaction info",
					a.error);
				throw "Failed to update Google Pay Amount. Please check the logs";
			}
			b.iframeField.contentWindow.postMessage(
			{
				action: "callback-result",
				clbParams:
				{
					callbackName: "googlePaySdk.transactionInfo",
					callbackArgs: a.response
				}
			}, "*")
		}).catch((a) =>
		{
			console.error("updateGooglePayAmount. An error occured", JSON.stringify(a));
			throw "Failed to update Google Pay Amount. Please check the logs";
		})
	},
	setIframeField: function (a)
	{
		this.iframeField = a;
		if (typeof this.iframeField === "string") this.iframeField = document.getElementById(this.iframeField) ||
			getByCustomAttribute("data-ifields-id", this.iframeField);
		if (!this.iframeField || !this.iframeField.contentWindow) this.iframeField = getByCustomAttribute("data-ifields-id", GP_IFIELD_NAME);
		if (!this.iframeField) throw "Cannot enable Google Pay. iframe with Google Pay button not found. Please refer to documentation.";
	},
	load: function ()
	{
		if (this.isEnabled && !this.isButtonLoaded)
		{
			if (!this.iframeField) throw "Cannot load Google Pay. iframe with Google Pay button not found. Please refer to documentation.";
			if (!this.iframeField.attributes["data-ifields-oninit"] ||
				!this.iframeField.attributes["data-ifields-oninit"].value) throw "Invalid setup. 'data-ifields-oninit' attribute must be set for Google Pay ifield. Please refer to documentation";
			let a = execFunction(this.iframeField.attributes["data-ifields-oninit"].value);
			if (!a) throw "Invalid setup. 'data-ifields-oninit' must be a function that returns a valid object. Please refer to documentation";
			this.environment = a.environment;
			this.onGetTransactionInfo = a.onGetTransactionInfo;
			a.merchantInfo.merchantOrigin = window.location.hostname;
			a.gpLoadedCallback = "ckGooglePay.gpLoadedCallback";
			this.iframeField.contentWindow.postMessage(
			{
				action: "init",
				tokenType: GP_IFIELD_NAME,
				params: a
			}, "*")
		}
	},
	gpLoadedCallback: function (a)
	{
		(this.isButtonLoaded = a && a.status === iStatus.success) && clearTimeout(this.timeoutId);
		this.isDebug && console.log("GP isButtonLoaded", this.isButtonLoaded)
	},
	authorize: function (a)
	{
		return new Promise(function (b, c)
		{
			var d = new XMLHttpRequest;
			d.open("POST", "https://cardknoxdev.com/gpay/home/AuthorizeFull");
			d.onload = function ()
			{
				this.status >=
					200 && this.status < 300 ? b(d.response) : c(
					{
						status: this.status,
						statusText: d.statusText
					})
			};
			d.onerror = function ()
			{
				c(
				{
					status: this.status,
					statusText: d.statusText
				})
			};
			d.setRequestHeader("Content-Type", "application/json");
			d.send(JSON.stringify(a))
		})
	}
};

function enableApplePay(a)
{
	ckApplePay.enableApplePay(a)
}
window.ckApplePay = {
	enableApplePay: function (a)
	{
		this.isDebug = isDebugEnv || a && a.isDebug;
		window.apReq = null;
		const b = a && a.initFunction;
		try
		{
			if (typeof b === "function") window.apReq = b();
			else if (typeof b === "string") window.apReq = execFunction(b);
			else throw "params.initFunction is required. Must be a function to initialize Apple Pay";
			if (!window.apReq) throw "Invalid response from init function. Please refer to documentation";
			applePaySdk.init(window.apReq)
		}
		catch (d)
		{
			throw `Cannot enable Apple Pay: ${d}`;
		}
		let c = a && a.amountField;
		typeof c === "string" && (c = document.getElementById(c));
		if (c && window.apReq)
		{
			const a = function ()
			{
				return roundTo(c.value || "0", 2)
			};
			let b = a();
			const f = this,
				g = async function (c)
				{
					try
					{
						let c = a();
						if (c !== b)
						{
							ckApplePay.updateAmount();
							b = c
						}
					}
					catch (h)
					{
						console.error("Amount Change failed:", exMsg(h));
						f.isDebug && alert("Amount Change failed: " + exMsg(h))
					}
				};
			amountEvents.forEach((a) =>
			{
				c.addEventListener(a, g)
			})
		}
		pingIfields()
	},
	updateAmount: function ()
	{
		window.apReq && execCallback(
		{
			callbackName: window.apReq.onGetTransactionInfo
		}).then((a) =>
		{
			if (!a) throw "Failed to update Apple Pay Amount. Please try again";
			if (a.error)
			{
				console.error("updateApplePayAmount. Error getting transaction info", a.error);
				throw "Failed to update Apple Pay Amount. please check the logs";
			}
			execCallback(
			{
				callbackName: "applePaySdk.setTransactionInfo",
				callbackArgs: a.response
			})
		}).catch((a) =>
		{
			console.error("updateApplePayAmount. An error occured", JSON.stringify(a));
			throw "Failed to update Apple Pay Amount. please check the logs";
		})
	},
	getSession: function (a)
	{
		return new Promise(function (b,
			c)
		{
			try
			{
				var d = new XMLHttpRequest;
				d.open("POST", "https://cardknoxdev.com/aptest/home/CreateSession");
				d.onload = function ()
				{
					this.status >= 200 && this.status < 300 ? b(d.response) : c(
					{
						status: this.status,
						statusText: d.response
					})
				};
				d.onerror = function ()
				{
					c(
					{
						status: this.status,
						statusText: d.statusText
					})
				};
				d.setRequestHeader("Content-Type", "application/json");
				d.send(JSON.stringify(
				{
					validationUrl: a
				}))
			}
			catch (e)
			{
				setTimeout(function ()
				{
					alert("getApplePaySession error: " + exMsg(e))
				}, 100)
			}
		})
	},
	authorize: function (a)
	{
		return new Promise(function (b,
			c)
		{
			var d = new XMLHttpRequest;
			d.open("POST", "https://cardknoxdev.com/aptest/home/Authorize");
			d.onload = function ()
			{
				this.status >= 200 && this.status < 300 ? b(d.response) : c(
				{
					status: this.status,
					statusText: d.statusText
				})
			};
			d.onerror = function ()
			{
				c(
				{
					status: this.status,
					statusText: d.statusText
				})
			};
			d.setRequestHeader("Content-Type", "application/json");
			d.send(JSON.stringify(a))
		})
	}
};
window.ckClick2Pay = {
	enableClickToPay: function (a)
	{
		if (!a) throw "Click-To-Pay parameters required";
		this.isDebug = isDebugEnv || a.isDebug;
		window.click2payRequest = null;
		const b = a.initFunction;
		try
		{
			window.click2payRequest = typeof b === "function" ? b() : typeof b === "string" ? execFunction(b) : a;
			if (!window.click2payRequest) throw "Invalid response from init function. Please refer to documentation";
			click2pay.init(window.click2payRequest)
		}
		catch (c)
		{
			throw `Cannot enable Click-To-Pay: ${c}`;
		}
	}
};
window.ckCustomerAgreement = {
	enableAgreement: function (a)
	{
		this.isDebug = isDebugEnv || a && a.isDebug;
		a.iframeField && this.setIframeField(a.iframeField);
		this.reqAgreement = Object.assign(
		{},
		{
			xKey: a.xKey,
			autoAgree: a.autoAgree,
			callbackName: a.callbackName,
			referrer: window.location.toString(),
			agreementLoadedCallback: "ckCustomerAgreement.agreementLoadedCallback",
			isDebug: a.isDebug
		});
		this.isAgreementLoaded = false;
		this.isFrameLoaded ? this.loadAgreement() : setTimeout(() =>
		{
			pingIfields()
		}, 100)
	},
	loadAgreement: function ()
	{
		this.iframeField.contentWindow.postMessage(
		{
			action: "init",
			tokenType: AGR_IFIELD_NAME,
			params: this.reqAgreement
		}, "*")
	},
	getToken: function ()
	{
		return new Promise((a, b) =>
		{
			this["getToken-resolve"] = a;
			this["getToken-reject"] = b;
			sendCallback(AGR_IFIELD_NAME,
			{
				client:
				{
					callbackName: "custAgreement.sendAcceptance",
					callbackType: "promise"
				},
				server:
				{
					promise:
					{
						resolve: "ckCustomerAgreement.callbackSuccess",
						reject: "ckCustomerAgreement.callbackReject"
					},
					callbackArgs: "getToken"
				}
			}, this.iframeField)
		})
	},
	setIframeField: function (a)
	{
		this.iframeField = a;
		if (typeof this.iframeField === "string") this.iframeField =
			document.getElementById(this.iframeField) || getByCustomAttribute("data-ifields-id", this.iframeField);
		if (!this.iframeField || !this.iframeField.contentWindow) this.iframeField = getByCustomAttribute("data-ifields-id", AGR_IFIELD_NAME);
		if (!this.iframeField) throw "Cannot enable Customer Agreement. iframe with Customer Agreement not found. Please refer to documentation.";
	},
	agreementLoadedCallback: function (a)
	{
		a && a.status !== iStatus.success && console.error("Failed to load Customer Agreement", a.statusText);
		this.isAgreementLoaded =
			a && a.status === iStatus.success
	},
	callbackSuccess: function (a, b)
	{
		try
		{
			b = JSON.parse(b)
		}
		catch (c)
		{}
		this[a + "-resolve"](b)
	},
	callbackReject: function (a, b)
	{
		try
		{
			b = JSON.parse(b)
		}
		catch (c)
		{}
		this[a + "-reject"](b)
	}
};
const MAX_VERSION = 100,
	APRequiredFeaturesMap = {
		address_validation: 3,
		support_recurring: 13,
		support_subscription: 13
	},
	applePaySdk = {
		defPaymentRequest:
		{
			buttonOptions:
			{
				buttonContainer: null,
				buttonColor: APButtonColor.black,
				buttonType: APButtonType.pay,
				width: null,
				height: null,
				minWidth: null,
				minHeight: null
			},
			walletCheckEnabled: false,
			applicationData: null,
			merchantIdentifier: null,
			merchantCapabilities: ["supports3DS"],
			supportedNetworks: ["amex", "discover", "masterCard", "visa"],
			supportedCountries: [],
			countryCode: "US",
			currencyCode: "USD",
			requiredFeatures: [],
			requiredBillingContactFields: ["postalAddress", "name"],
			billingContact: null,
			shippingContact: null,
			shippingType: null,
			shippingMethods: [],
			total: null,
			lineItems: null,
			setShippingMethods: function (a)
			{
				this.shippingMethods = a
			},
			isFeatureRequested: function (...a)
			{
				return a.some((a) =>
				{
					return this.requiredFeatures.includes(a)
				})
			},
			validateFeatures: function (a)
			{
				if (a)
				{
					if (a.error && !this.isFeatureRequested(APRequiredFeatures.address_validation)) throw "Error. A Required Feature 'address_validation' must be set. Please refer to documentation on how to set 'requiredFeatures' for Address Validation";
					if (a.lineItems && !this.isFeatureRequested(APRequiredFeatures.support_recurring, APRequiredFeatures.support_subscription) && a.lineItems.some((a) =>
						{
							return a.paymentTiming || a.recurringPaymentStartDate || a.recurringPaymentIntervalUnit || a.recurringPaymentIntervalCount || a.deferredPaymentDate || a.automaticReloadPaymentThresholdAmount
						})) throw "Error. A Required Feature 'support_recurring' or 'support_subscription' must be set. Please refer to documentation on how to set 'requiredFeatures' for Recurring/Subscription";
				}
			}
		},
		sessionCallbacks:
		{
			getApplePayError: function (a)
			{
				if (a)
				{
					if (!a.code) throw "'error.code' is required. For available codes refer to 'APErrorCode'";
					logError("ApplePayError custom error", a);
					return new ApplePayError(a.code, a.contactField, a.message)
				}
				return null
			},
			clbNamePaymentComplete: null,
			clbNameCancel: null,
			onCancel: function (a)
			{
				self = applePaySdk;
				logError("Apple Pay Session canceled", a);
				self.sessionCallbacks.clbNameCancel && execCallback(
				{
					callbackName: self.sessionCallbacks.clbNameCancel,
					callbackArgs: a
				})
			},
			clbNameValidateMerchant: null,
			onValidateMerchant: async function (a)
			{
				const b = applePaySdk;
				try
				{
					if (!a.isTrusted)
					{
						logError("onValidateMerchant", "Not trusted");
						b.session.abort()
					}
					const c = await b.buildCallback(b.sessionCallbacks.clbNameValidateMerchant, a.validationURL, "promise"),
						d = JSON.parse(c);
					b.session.completeMerchantValidation(d)
				}
				catch (c)
				{
					logError("onValidateMerchant error", c);
					b.session.abort()
				}
			},
			clbNamePaymentAuthorize: null,
			onPaymentAuthorize: async function (a)
			{
				const b = applePaySdk;
				try
				{
					if (!a.isTrusted)
					{
						logError("onPaymentAuthorize",
							"Not trusted");
						b.session.abort()
					}
					const c = await b.buildCallback(b.sessionCallbacks.clbNamePaymentAuthorize, a.payment, "promise");
					b.session.completePayment(ApplePaySession.STATUS_SUCCESS);
					b.sessionCallbacks.clbNamePaymentComplete && execCallback(
					{
						callbackName: b.sessionCallbacks.clbNamePaymentComplete,
						callbackArgs:
						{
							response: c
						}
					})
				}
				catch (c)
				{
					logError("onPaymentAuthorize error", c);
					b.session.abort();
					b.sessionCallbacks.clbNamePaymentComplete && execCallback(
					{
						callbackName: b.sessionCallbacks.clbNamePaymentComplete,
						callbackArgs:
						{
							error: c
						}
					})
				}
			},
			clbNameShippingContactSelected: null,
			onShippingContactSelected: async function (a)
			{
				const b = applePaySdk;
				try
				{
					if (!a.isTrusted)
					{
						logError("onShippingContactSelected", "Not trusted");
						b.session.abort()
					}
					const c = await b.buildCallback.call(b, b.sessionCallbacks.clbNameShippingContactSelected, a.shippingContact, "promise");
					b.defPaymentRequest.validateFeatures.call(b.defPaymentRequest, c);
					if (b.defPaymentRequest.version >= 3)
					{
						const a = {
								newTotal: c.total,
								newLineItems: c.lineItems,
								newShippingMethods: c.shippingMethods
							},
							e = b.sessionCallbacks.getApplePayError(c.error);
						if (e) a.errors = [e];
						b.session.completeShippingContactSelection(a)
					}
					else b.session.completeShippingContactSelection(ApplePaySession.STATUS_SUCCESS, c.shippingMethods, c.total, c.lineItems)
				}
				catch (c)
				{
					logError("onShippingContactSelected error", c);
					b.session.abort()
				}
			},
			clbNameShippingMethodSelected: null,
			onShippingMethodSelected: async function (a)
			{
				const b = applePaySdk;
				try
				{
					if (!a.isTrusted)
					{
						logError("onShippingMethodSelected", "Not trusted");
						b.session.abort()
					}
					const c = await b.buildCallback.call(b,
						b.sessionCallbacks.clbNameShippingMethodSelected, a.shippingMethod, "promise");
					b.defPaymentRequest.validateFeatures.call(b.defPaymentRequest, c);
					if (b.defPaymentRequest.version >= 3)
					{
						const a = {
							newTotal: c.total,
							newLineItems: c.lineItems
						};
						if (b.defPaymentRequest.version >= 13 && c.shippingMethods) a.newShippingMethods = c.shippingMethods;
						b.session.completeShippingMethodSelection(a)
					}
					else b.session.completeShippingMethodSelection(ApplePaySession.STATUS_SUCCESS, c.total, c.lineItems)
				}
				catch (c)
				{
					logError("onShippingMethodSelected error",
						c);
					b.session.abort()
				}
			},
			clbNamePaymentMethodSelected: null,
			onPaymentMethodSelected: async function (a)
			{
				const b = applePaySdk;
				try
				{
					if (!a.isTrusted)
					{
						logError("onPaymentMethodSelected", "Not trusted");
						b.session.abort()
					}
					const c = await b.buildCallback.call(b, b.sessionCallbacks.clbNamePaymentMethodSelected, a.paymentMethod, "promise");
					b.defPaymentRequest.validateFeatures.call(b.defPaymentRequest, c);
					if (b.defPaymentRequest.version >= 3)
					{
						const a = {
							newTotal: c.total,
							newLineItems: c.lineItems
						};
						if (b.defPaymentRequest.version >=
							13)
						{
							if (c.shippingMethods) a.newShippingMethods = c.shippingMethods;
							const d = b.sessionCallbacks.getApplePayError(c.error);
							if (d) c.errors = [d]
						}
						b.session.completePaymentMethodSelection(a)
					}
					else b.session.completePaymentMethodSelection(c.total, c.lineItems)
				}
				catch (c)
				{
					logError("onPaymentMethodSelected error", c);
					b.session.abort()
				}
			}
		},
		beforeProcessPayment: async function ()
		{
			try
			{
				const a = applePaySdk;
				if (typeof a.onBeforeProcessPayment === "string")
				{
					const b = await a.buildCallback(a.onBeforeProcessPayment, null, "promise");
					if (b !==
						iStatus.success) throw b;
				}
				return iStatus.success
			}
			catch (a)
			{
				throw a;
			}
		},
		init: function (a)
		{
			const b = function ()
				{
					if (!window.ApplePaySession || !ApplePaySession.canMakePayments()) throw "Apple Pay not supported";
					if (!a) throw "paymentRequest parameter is required. For more information please refer to documentation";
					if (!a.buttonOptions || !a.buttonOptions.buttonContainer || typeof a.buttonOptions.buttonContainer !== "string") throw "paymentRequest.buttonOptions.buttonContainer is required. Must be an id of div element where Apple Pay button will be loaded";
					if (!a.onGetTransactionInfo || typeof a.onGetTransactionInfo !== "string") throw "paymentRequest.onGetTransactionInfo is required. Must be a name of a function that returns a line item(ApplePayLineItem) representing the total for the payment. For more info please refer to documentation";
					if (!a.onValidateMerchant || typeof a.onValidateMerchant !== "string") throw "paymentRequest.onValidateMerchant is required. Must be a name of a function that validates  Apple Pay session. For more info please refer to documentation";
					if (!a.onPaymentAuthorize || typeof a.onPaymentAuthorize !== "string") throw "paymentRequest.onPaymentAuthorize is required. Must be a name of a function that authorizes payment with Gateway. For more info please refer to documentation";
					if (a.onPaymentComplete && typeof a.onPaymentComplete !== "string") throw "paymentRequest.onPaymentComplete must be a name of a function that handles response from the Gateway. For more info please refer to documentation";
				},
				c = () =>
				{
					let a = 1,
						b = MAX_VERSION,
						c;
					for (; b - a > 1;)
					{
						c = roundToNumber((b +
							a) / 2, 0);
						ApplePaySession.supportsVersion(c) ? a = c : b = c
					}
					return a
				},
				d = () =>
				{
					if (!a.requiredFeatures) return 1;
					var b = a.requiredFeatures.map((a) =>
					{
						return APRequiredFeaturesMap[a]
					});
					b = Math.max(...b);
					return !b || b < 1 ? 1 : b
				};
			try
			{
				const e = this;
				this.isDebug = a.isDebug;
				b();
				Object.assign(this.defPaymentRequest, a);
				const f = c(),
					g = d();
				logDebug(`minRequiredVersion: ${g}; supportedVersion: ${f}`, this.isDebug);
				if (f < g) throw "Minimum Apple Pay required version is not supported by this device.\nPlease upgrade your iOS version.";
				this.defPaymentRequest.version =
					f;
				this.buildCallback(a.onGetTransactionInfo).then(function (a)
				{
					e.setTransactionInfo.call(e, a)
				}).catch(function (a)
				{
					logError("onGetTransactionInfo error", a)
				});
				a.onGetShippingMethods && this.buildCallback(a.onGetShippingMethods).then(function (a)
				{
					e.defPaymentRequest.setShippingMethods.call(e.defPaymentRequest, a)
				}).catch(function (a)
				{
					logError("onGetShippingMethods error", a)
				});
				e.defPaymentRequest.walletCheckEnabled ? ApplePaySession.canMakePaymentsWithActiveCard(e.defPaymentRequest.merchantIdentifier).then(function (b)
				{
					if (!b) throw "Apple Pay not supported";
					e.showApplePayButton();
					chained([a, "onAPButtonLoaded"]) && execCallback(
					{
						callbackName: a.onAPButtonLoaded,
						callbackArgs:
						{
							status: iStatus.success
						}
					})
				}).catch(function (a)
				{
					throw a;
				}) : setTimeout(function ()
				{
					e.showApplePayButton();
					chained([a, "onAPButtonLoaded"]) && execCallback(
					{
						callbackName: a.onAPButtonLoaded,
						callbackArgs:
						{
							status: iStatus.success
						}
					})
				}, 10);
				this.onBeforeProcessPayment = a.onBeforeProcessPayment;
				if (a.onValidateMerchant) this.sessionCallbacks.clbNameValidateMerchant = a.onValidateMerchant;
				if (a.onPaymentAuthorize) this.sessionCallbacks.clbNamePaymentAuthorize =
					a.onPaymentAuthorize;
				if (a.onPaymentComplete) this.sessionCallbacks.clbNamePaymentComplete = a.onPaymentComplete;
				if (a.onCancel) this.sessionCallbacks.clbNameCancel = a.onCancel;
				if (a.onShippingContactSelected) this.sessionCallbacks.clbNameShippingContactSelected = a.onShippingContactSelected;
				if (a.onShippingMethodSelected) this.sessionCallbacks.clbNameShippingMethodSelected = a.onShippingMethodSelected;
				if (a.onPaymentMethodSelected) this.sessionCallbacks.clbNamePaymentMethodSelected = a.onPaymentMethodSelected
			}
			catch (e)
			{
				logError("Apple Pay initialization failed",
					e);
				chained([a, "onAPButtonLoaded"]) && (/not supported/i.test(e) ? execCallback(
				{
					callbackName: a.onAPButtonLoaded,
					callbackArgs:
					{
						status: iStatus.unsupported
					}
				}) : execCallback(
				{
					callbackName: a.onAPButtonLoaded,
					callbackArgs:
					{
						status: iStatus.error,
						reason: "Failed to initialize Apple Pay. Please check the logs"
					}
				}))
			}
		},
		setTransactionInfo: function (a)
		{
			if (a)
			{
				this.defPaymentRequest.validateFeatures.call(this.defPaymentRequest, a);
				this.defPaymentRequest.total = a.total;
				this.defPaymentRequest.lineItems = a.lineItems
			}
		},
		getPaymentRequest: function ()
		{
			return {
				merchantCapabilities: this.defPaymentRequest.merchantCapabilities,
				supportedNetworks: this.defPaymentRequest.supportedNetworks,
				supportedCountries: this.defPaymentRequest.supportedCountries,
				countryCode: this.defPaymentRequest.countryCode,
				currencyCode: this.defPaymentRequest.currencyCode,
				requiredBillingContactFields: this.defPaymentRequest.requiredBillingContactFields,
				billingContact: this.defPaymentRequest.billingContact,
				requiredShippingContactFields: this.defPaymentRequest.requiredShippingContactFields,
				shippingContact: this.defPaymentRequest.shippingContact,
				shippingMethods: this.defPaymentRequest.shippingMethods,
				total: this.defPaymentRequest.total,
				lineItems: this.defPaymentRequest.lineItems
			}
		},
		createSession: function ()
		{
			const a = this.getPaymentRequest();
			this.session = new ApplePaySession(this.defPaymentRequest.version, a);
			this.session.merchantIdentifier = this.defPaymentRequest.merchantIdentifier;
			this.session.oncancel = this.sessionCallbacks.onCancel;
			if (this.sessionCallbacks.clbNameValidateMerchant) this.session.onvalidatemerchant = this.sessionCallbacks.onValidateMerchant;
			if (this.sessionCallbacks.clbNamePaymentAuthorize) this.session.onpaymentauthorized =
				this.sessionCallbacks.onPaymentAuthorize;
			if (this.sessionCallbacks.clbNameShippingContactSelected) this.session.onshippingcontactselected = this.sessionCallbacks.onShippingContactSelected;
			if (this.sessionCallbacks.clbNameShippingMethodSelected) this.session.onshippingmethodselected = this.sessionCallbacks.onShippingMethodSelected;
			if (this.sessionCallbacks.clbNamePaymentMethodSelected) this.session.onpaymentmethodselected = this.sessionCallbacks.onPaymentMethodSelected;
			return this.session
		},
		showApplePayButton: function ()
		{
			HTMLCollection.prototype[Symbol.iterator] =
				Array.prototype[Symbol.iterator];
			var a = [".apple-pay-button.visible { ", "visibility: visible; ", "} ", ".apple-pay-button:active { ", "background-color: rgb(152, 152, 152); ", "}", ".apple-pay-button { ", "visibility: hidden;", "cursor: pointer;", "-webkit-appearance: -apple-pay-button;", "-apple-pay-button-type: " + (this.defPaymentRequest.buttonOptions.buttonType || APButtonType.pay) + ";", "-apple-pay-button-style: " + (this.defPaymentRequest.buttonOptions.buttonColor || APButtonColor.black) + ";"];
			this.defPaymentRequest.buttonOptions.minWidth &&
				a.push("min-width: " + this.defPaymentRequest.buttonOptions.minWidth + "px;");
			this.defPaymentRequest.buttonOptions.minHeight && a.push("min-height: " + this.defPaymentRequest.buttonOptions.minHeight + "px;");
			this.defPaymentRequest.buttonOptions.width && a.push("width: " + this.defPaymentRequest.buttonOptions.width + ";");
			this.defPaymentRequest.buttonOptions.height && a.push("height: " + this.defPaymentRequest.buttonOptions.height + ";");
			a.push("}");
			a = a.join("\n");
			const b = document.getElementById(this.defPaymentRequest.buttonOptions.buttonContainer);
			if (!b) throw "Invalid buttonContainer. Must be an id of div element where Apple Pay button will be loaded";
			const c = document.head || document.getElementsByTagName("head")[0],
				d = document.createElement("style");
			c.appendChild(d);
			d.type = "text/css";
			d.styleSheet ? d.styleSheet.cssText = a : d.appendChild(document.createTextNode(a));
			b.className = b.className + " apple-pay-button";
			b.className = b.className + " visible";
			b.addEventListener("click", this.payButtonClicked)
		},
		payButtonClicked: async function (a)
		{
			try
			{
				const a = applePaySdk.createSession.call(applePaySdk),
					c = await applePaySdk.beforeProcessPayment();
				if (c !== iStatus.success) throw c;
				a.begin()
			}
			catch (b)
			{
				logError("Apple Pay session error", b)
			}
		},
		buildCallback: async function (a, b, c)
		{
			try
			{
				const d = await execCallback(
				{
					callbackName: a,
					callbackType: c,
					callbackArgs: b
				});
				if (d.error) throw d.error;
				return d.response
			}
			catch (d)
			{
				logError("buildCallback", d);
				throw d;
			}
		},
		callbackSuccess: function (a, b)
		{
			this[a + "-resolve"](b)
		},
		callbackReject: function (a, b)
		{
			this[a + "-reject"](b)
		}
	};
const c2pEnvironment = {
		sandbox: 0,
		production: 1
	},
	c2pReviewAction = {
		pay: "PAY",
		continue: "CONTINUE"
	},
	c2pCardContainerType = {
		iframe: "iframe",
		window: "window",
		isValid: function (a)
		{
			return a === this.iframe || a === this.window
		}
	},
	c2pCfg = {
		sdkSettings:
		{
			urls: ["https://sandbox.secure.checkout.visa.com/checkout-widget/resources/js/integration/v1/sdk.js", "https://secure.checkout.visa.com/checkout-widget/resources/js/integration/v1/sdk.js"],
			assets: ["https://sandbox-assets.secure.checkout.visa.com/wallet-services-web/xo/button.png",
				"https://assets.secure.checkout.visa.com/wallet-services-web/xo/button.png"
			],
			apiKeys: ["B0M69G64XJA427VYVR9D21LREy95j7ZjigC1LjlMb6VNVJjnI"],
			encryptionKeys: ["X1EMH2Z1WAY3G2AC50EJ13uIfP5GhdOFH1owxti6T0x2BXcX8"],
			sandboxExternalClientId: "test1",
			settings:
			{
				locale: "en_US",
				countryCode: "US",
				displayName: "Cardknox",
				acceptedRegions: "US",
				dataLevel: "FULL",
				payment:
				{
					cardBrands: ["VISA", "MASTERCARD", "AMEX", "DISCOVER"],
					billingCountries: ["US", "CA"]
				},
				review:
				{
					message: "Please confirm your payment",
					buttonAction: c2pReviewAction.pay
				},
				shipping:
				{
					acceptedRegions: ["US", "CA"],
					collectShipping: "true"
				},
				tokenizationSetup:
				{
					enableTokenization: true,
					tokenCryptogramType: "TAVV"
				}
			}
		},
		imageBaseUri: "https://cdn.cardknox.com/ifields/images/c2p/",
		cardContainerType: c2pCardContainerType.iframe,
		forceClickToPayButton: false,
		setConfiguration: function (a)
		{
			this.environment = a.environment;
			this.apiKey = this.sdkSettings.apiKeys[this.environment];
			this.encryptionKey = this.sdkSettings.encryptionKeys[this.environment];
			this.c2pBtnImg = this.sdkSettings.assets[this.environment] +
				"?cardBrands=VISA,MASTERCARD,DISCOVER,AMEX";
			this.cardContainerType = a.cardContainerType || c2pCardContainerType.iframe;
			this.click2payContainer = a.click2payContainer;
			this.mainCssClass = a.mainCssClass || "c2p-def";
			this.forceClickToPayButton = a.forceClickToPayButton;
			this.displayWaitScreenAfterCheckout = isDefined(a.displayWaitScreenAfterCheckout) ? a.displayWaitScreenAfterCheckout : true;
			this.externalClientId = a.externalClientId;
			this.isDebug = a.isDebug || this.environment == c2pEnvironment.sandbox;
			if (a.reviewAction) this.sdkSettings.settings.review.buttonAction =
				a.reviewAction
		},
		logDebug: function (...a)
		{
			this.isDebug && console.log.apply(console.log, a)
		},
		convertEnvironment: function (a)
		{
			if (typeof a === "string")
			{
				const b = Object.keys(c2pEnvironment).find((b) =>
				{
					return a === b
				});
				if (b) return c2pEnvironment[b]
			}
			return a
		}
	};
const c2pHelper = {
	currDate: (() =>
	{
		const a = new Date;
		return Number(`${a.getFullYear()}${("0"+(a.getMonth()+1)).slice(-2)}`)
	})(),
	getConsumerIdentity: function (a, b)
	{
		if (!b)
			if (/\w+@\w+/.test(a)) b = "EMAIL";
			else throw "Invalid identityValue and identityType";
		b = b.toUpperCase();
		if (["EMAIL", "EMAIL_ADDRESS"].includes(b)) b = "EMAIL";
		else
		{
			throw "Invalid identityType, only 'EMAIL' is valid";
			b = void 0
		}
		return {
			identityProvider: "SRC",
			identityValue: a,
			type: b
		}
	},
	getError: function (a)
	{
		let b = "";
		a.name && (b += `name:'${a.name}';`);
		a.reason &&
			(b += `reason:'${a.status}';`);
		a.err && (a.err.message && (b += `errMsg:'${a.err.message}'`));
		return b
	},
	getCardType: function (a)
	{
		return /^4/.test(a) ? "visa" : /^(5[1-5][0-9]{14}|2(22[1-9][0-9]{12}|2[3-9][0-9]{13}|[3-6][0-9]{14}|7[0-1][0-9]{13}|720[0-9]{12}))$/.test(a) ? "mc" : /^3[47]/.test(a) ? "amex" : null
	},
	getCardBrandByCardType: function (a)
	{
		a = this.getCardType(a);
		if (!a) throw "This Card Brand is not supported";
		return a
	},
	validateCardData: function (a)
	{
		if (a && (a.panExpirationYear && a.panExpirationYear.length < 4)) a.panExpirationYear =
			"20" + a.panExpirationYear.substr(-2);
		return a
	},
	validateEmail: function (a)
	{
		return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(a)
	},
	isFunction: function (a)
	{
		return typeof a === "function"
	},
	configureCardContainer: function (a)
	{
		if (a && c2pCfg.cardContainerType === c2pCardContainerType.iframe) a = a.contentWindow;
		return a
	},
	isiframeSupported: function ()
	{
		return !/apple/i.test(navigator.vendor)
	},
	getCardContainer: function (a)
	{
		if (a ===
			"VISA") return null;
		a = null;
		if (!this.isiframeSupported) c2pCfg.cardContainerType = c2pCardContainerType.window;
		if (c2pCfg.cardContainerType === c2pCardContainerType.iframe)
		{
			a = document.getElementById("cardContainer");
			a.style = ""
		}
		return a ? a.contentWindow : window.open("about:blank", "dcf", "width=430, height=760, location=no")
	},
	closeCardContainer: function (a)
	{
		if (a)
			if (c2pCfg.cardContainerType === c2pCardContainerType.iframe)
			{
				a = document.getElementById("cardContainer");
				a.style = "display:none";
				a.src = "about:blank"
			}
		else a.close()
	},
	isCardExpired: function (a)
	{
		return a.digitalCardData.status === "EXPIRED" || Number(a.panExpirationYear + a.panExpirationMonth) < this.currDate
	},
	sortCards: function (a, b)
	{
		return a.expired && !b.expired ? 1 : !a.expired && b.expired ? -1 : b.index - a.index
	},
	buildProfilesScreen: async function (a, b, c)
	{
		if (!a || !a.maskedCards) return null;
		let d = [],
			e = 0;
		a.maskedCards.forEach((a) =>
		{
			try
			{
				c2pCfg.logDebug("card:", JSON.stringify(a));
				const b = new Promise((b, d) =>
				{
					try
					{
						if (a.digitalCardData.status === "ACTIVE" || a.digitalCardData.status === "EXPIRED")
						{
							const d =
								document.createElement("tr");
							d.className = "c2p-card-row";
							d.addEventListener("click", async () =>
							{
								c(
								{
									cardId: a.srcDigitalCardId,
									cardNetwork: a.network
								})
							});
							d.addComponent("td",
							{
								class: "c2p-card"
							}).addComponent("img",
							{
								src: `${a.digitalCardData.artUri}`,
								class: "c2p-card-img"
							});
							d.addComponent("td",
							{
								class: "c2p-brand"
							}).addComponent("img", c2pHelper.getCardBrandImage(a.network));
							d.addComponent("td",
							{
								class: "c2p-card-number"
							}).addComponent("span",
							{
								class: "c2p-card-txt",
								innerHTML: `${a.digitalCardData.presentationName||
""}****${a.panLastFour}`
							});
							const e = d.addComponent("td",
								{
									class: "c2p-card-expired"
								}),
								g = this.isCardExpired(a);
							g && e.addComponent("span",
							{
								innerHTML: "expired"
							});
							d.addComponent("td",
							{
								class: "c2p-card-arrow"
							}).addComponent("img",
							{
								src: c2pCfg.imageBaseUri + "arrow.png"
							});
							b(
							{
								index: this.maxDate(a.dateOfCardLastUsed, a.dateOfCardCreated),
								expired: g,
								payload: d
							})
						}
					}
					catch (l)
					{
						logError(`Error for ${a.panLastFour}`, l);
						b(
						{
							index: e++,
							payload: null
						})
					}
				});
				d.push(b)
			}
			catch (g)
			{
				logError("Error loading profile", g)
			}
		});
		if (d.length > 0)
		{
			a =
				b.addComponent("div",
				{
					class: "c2p-form"
				});
			a.addComponent("div",
			{
				class: "c2p-header"
			}).addComponent("img",
			{
				src: c2pCfg.imageBaseUri + "c2p-cards.png"
			});
			a = a.addComponent("div");
			const c = this.buildComponent("table", a,
			{
				id: "profilesTable",
				class: "c2p-table-card"
			});
			if ((a = await Promise.all(d)) && a.length > 0)
			{
				a.sort((a, b) =>
				{
					return this.sortCards(a, b)
				});
				a.forEach((a) =>
				{
					a.payload && c.appendChild(a.payload)
				})
			}
			return c
		}
		return null
	},
	getCardBrandImage: function (a)
	{
		switch (a.toUpperCase())
		{
		case "VISA":
			return {
				src: c2pCfg.imageBaseUri +
					"c2p-visa-icon.png", class: "c2p-visa-icon-img"
			};
		case "MASTERCARD":
			return {
				src: c2pCfg.imageBaseUri + "c2p-mc-icon.png", class: "c2p-mc-icon-img"
			};
		case "DISCOVER":
			return {
				src: c2pCfg.imageBaseUri + "c2p-discover-icon.png", class: "c2p-discover-icon-img"
			};
		default:
			return {
				src: c2pCfg.imageBaseUri + "c2p-amex-icon.png", class: "c2p-amex-icon-img"
			}
		}
	},
	buildNoProfilesScreen: function (a)
	{
		a = a.addComponent("div",
		{
			class: "c2p-form"
		});
		a.addComponent("div",
		{
			class: "c2p-header"
		}).addComponent("img",
		{
			src: c2pCfg.imageBaseUri + "c2p-cards.png"
		});
		a = a.addComponent("div",
		{
			class: "c2p-no-profiles"
		});
		a.addComponent("span",
		{
			innerHTML: "No cards found"
		});
		a.addComponent("a",
		{
			href: "#",
			innerHTML: "Use different login"
		}).addEventListener("click", async () =>
		{
			click2pay.resetScreen.call(click2pay, true, true)
		})
	},
	buildWaitScreen: function (a = null)
	{
		return new Promise((b, c) =>
		{
			setTimeout(() =>
			{
				try
				{
					const c = this.getElement(a && a.parent) || c2pCfg.click2payContainer,
						e = c2pHelper.buildComponent("div", c,
						{
							id: "waitDivId",
							class: c2pCfg.mainCssClass
						});
					if (a && isDefined(a.showHeader) &&
						!a.showHeader) e.addWaitComponent(a && a.imgWidth, a && a.imgHeight);
					else
					{
						const b = e.addComponent("div",
						{
							class: "c2p-form",
							style: "height:100px; display:inline-block"
						});
						b.addComponent("div",
						{
							class: "c2p-header"
						}).addComponent("img",
						{
							src: c2pCfg.imageBaseUri + "c2p-cards.png"
						});
						b.addWaitComponent(a && a.imgWidth, a && a.imgHeight)
					}
					b(e)
				}
				catch (d)
				{
					c(d)
				}
			}, 1)
		})
	},
	buildSignInScreen: function (a, b)
	{
		const c = () =>
			{
				if (!this.signinSubmitted)
				{
					this.signinSubmitted = true;
					const a = e.addWaitComponent();
					setTimeout(() =>
					{
						try
						{
							if (d()) b(d());
							else
							{
								this.signinSubmitted = false;
								this.removeComponent(a)
							}
						}
						catch (m)
						{
							this.signinSubmitted = false;
							this.removeComponent(a)
						}
					}, 1)
				}
			},
			d = () =>
			{
				const a = document.getElementById("txtEmail"),
					b = document.getElementById("lblEmail"),
					c = document.getElementById("lblError");
				if (this.validateEmail(a.value))
				{
					c.innerHTML = "";
					b.classList.remove("invalid");
					a.classList.remove("invalid")
				}
				else
				{
					c2pCfg.logDebug("invalid email");
					c.innerHTML = a.value ? "Invalid Email" : "";
					b.classList.add("invalid");
					a.classList.add("invalid");
					return null
				}
				return this.getConsumerIdentity(a.value,
					"EMAIL")
			};
		this.signinSubmitted = false;
		const e = a.addComponent("div",
		{
			class: "c2p-form"
		});
		e.addComponent("div",
		{
			class: "c2p-header"
		}).addComponent("img",
		{
			src: c2pCfg.imageBaseUri + "c2p-cards.png"
		});
		a = e.addComponent("div",
		{
			class: "c2p-body"
		}).addComponent("div",
		{
			class: "inputs"
		});
		a.addComponent("div",
		{
			class: "body-text",
			innerHTML: "Enter email to access your cards<br />"
		});
		a = a.addComponent("div",
		{
			class: "div-signin"
		});
		a.addComponent("label",
		{
			id: "lblEmail",
			for: "txtEmail",
			class: "lbl-signin",
			innerHTML: "Email"
		});
		const f = a.addComponent("input",
		{
			type: "email",
			id: "txtEmail",
			class: "txt-signin",
			autocomplete: "on"
		});
		f.addEventListener("keypress", (a) =>
		{
			c2pHelper.handleDefaultButton(a, "c2pValidateButton")
		});
		a.addComponent("label",
		{
			id: "lblError",
			class: "error-label"
		});
		a.addComponent("a",
		{
			id: "c2pValidateButton",
			class: "c2p-btn",
			innerHTML: "Continue"
		}).addEventListener("click", () =>
		{
			return c()
		});
		setTimeout(() =>
		{
			f.focus()
		}, 10)
	},
	buildOtpScreen: function (a, b, c, d)
	{
		let e = 1;
		const f = function ()
			{
				let a = "";
				document.getElementsByName("txtOtp").forEach((b) =>
				{
					return a += b.value
				});
				return a
			},
			g = () =>
			{
				try
				{
					let a = true;
					document.getElementsByName("txtOtp").forEach((b) =>
					{
						if (b.value) b.classList.remove("invalid");
						else
						{
							b.classList.add("invalid");
							a = false
						}
					});
					if (!a) throw "Invalid OTP";
					return true
				}
				catch (p)
				{
					return false
				}
			},
			m = () =>
			{
				const a = k.addWaitComponent();
				setTimeout(async () =>
				{
					try
					{
						if (g())
						{
							const a = await b(f(), e);
							c2pCfg.logDebug(a)
						}
						else this.removeComponent(a)
					}
					catch (q)
					{
						document.getElementById("lblOtpError").innerHTML = q;
						this.removeComponent(a);
						e++
					}
				}, 1)
			},
			h = async () =>
			{
				const a =
					k.addWaitComponent();
				setTimeout(async () =>
				{
					try
					{
						l();
						document.getElementById("lblOtpError").innerHTML = "";
						const a = await d(c);
						c2pCfg.logDebug(a)
					}
					catch (q)
					{
						document.getElementById("lblOtpError").innerHTML = q
					}
					finally
					{
						this.removeComponent(a)
					}
				}, 1)
			}, l = () =>
			{
				const a = document.getElementById("divOtp").getElementsByTagName("input");
				for (let b = 0; b < a.length; b++) a[b].value = "";
				a[0].focus()
			}, k = a.addComponent("div",
			{
				class: "c2p-form"
			});
		k.addComponent("div",
		{
			class: "c2p-header"
		}).addComponent("img",
		{
			src: c2pCfg.imageBaseUri +
				"c2p-cards.png"
		});
		a = k.addComponent("div",
		{
			class: "c2p-body"
		}).addComponent("div",
		{
			class: "inputs"
		});
		a.addComponent("label",
		{
			class: "lbl-txt-otp",
			for: "txtOtp",
			innerHTML: "One-time code"
		});
		a.addComponent("br");
		a.addComponent("div",
		{
			id: "divOtp",
			class: "container-txt-otp"
		}).addOtpInputComponent(6,
		{
			name: "txtOtp",
			class: "txt-otp",
			maxlength: 1,
			size: 1,
			pattern: "[0-9]{1}"
		}, "c2pOtpButton", "lblOtpError");
		const n = a.addComponent("div",
		{
			class: "resend-otp"
		});
		n.addComponent("span",
		{
			innerHTML: "Didn't receive the code?"
		});
		n.addComponent("a",
		{
			class: "resend-otp-btn",
			href: "#",
			innerHTML: "Resend Now"
		}).addEventListener("click", () =>
		{
			return h()
		});
		a.addComponent("label",
		{
			id: "lblOtpError",
			class: "error-label"
		});
		a.addComponent("a",
		{
			id: "c2pOtpButton",
			class: "c2p-btn",
			innerHTML: "Continue"
		}).addEventListener("click", () =>
		{
			return m()
		})
	},
	getElement: function (a)
	{
		typeof a === "string" && (a = document.getElementById(a));
		return a && a.appendChild ? a : null
	},
	buildComponent: function (a, b, c)
	{
		if (!c || !c.id) throw "'settings.id' is required to build component.";
		const d = document.getElementById(c.id);
		if (d) return d;
		b = this.getElement(b);
		if (!b) throw Error("parent not found");
		return b.addComponent(a, c)
	},
	removeComponent: function (a)
	{
		typeof a === "string" && (a = document.getElementById(a));
		a && a.parentElement && a.parentElement.removeChild(a)
	},
	handleDefaultButton: function (a, b)
	{
		if (a.keyCode === 13)
		{
			a.preventDefault();
			a.stopPropagation();
			(a = document.getElementById(b)) && a.click()
		}
	},
	addCardContainer: function (a)
	{
		document.body.addComponent("iframe",
		{
			id: "cardContainer",
			sandbox: "allow-same-origin allow-scripts allow-forms allow-top-navigation allow-popups allow-modals",
			role: "presentation",
			style: "display:none"
		})
	},
	maxDate(a, b)
	{
		a = Date.parse(a);
		b = Date.parse(b);
		return a && b ? Math.max(a, b) : a || b
	},
	valOrDef: function (a, b)
	{
		return a ? a : b
	},
	callServer: async function (a, b)
	{
		return new Promise(function (c, d)
		{
			var e = new XMLHttpRequest;
			e.open("POST", a);
			e.onload = function ()
			{
				this.status >= 200 && this.status < 300 ? c(e.response) : d(
				{
					status: this.status,
					statusText: e.response
				})
			};
			e.onerror = function ()
			{
				d(
				{
					status: this.status,
					statusText: e.statusText
				})
			};
			if (b)
			{
				e.setRequestHeader("Content-Type", "application/json");
				e.send(JSON.stringify(b))
			}
			else e.send()
		})
	}
};
HTMLElement.prototype.addComponent = function (a, b)
{
	let c = document.createElement(a);
	if (b)
	{
		if (b.innerHTML)
		{
			c.innerHTML = b.innerHTML;
			delete b.innerHTML
		}
		Object.entries(b).forEach(([a, b]) =>
		{
			return c.setAttribute(a, b)
		})
	}
	return this.appendChild(c)
};
HTMLElement.prototype.addWaitComponent = function (a = null, b = null)
{
	const c = this.addComponent("div",
	{
		class: "wait"
	});
	a = a || 48;
	b = b || 48;
	c.addComponent("img",
	{
		src: c2pCfg.imageBaseUri + "wait.gif",
		style: `width:${a}px; height:${b}px;`
	});
	return c
};
HTMLElement.prototype.addOtpInputComponent = function (a, b, c, d)
{
	const e = [];
	b.style = "color: transparent; text-shadow: 0 0 0 gray;";
	for (let c = 0; c < a; c++)
	{
		let a = this.addComponent("input", b);
		a.dataIndex = c;
		e.push(a)
	}
	e[0].focus();
	const f = /\D/;
	for (a = 0; a < e.length; a++)
	{
		e[a].addEventListener("keydown", function (a)
		{
			let b = this.dataIndex;
			if (a.key === "Backspace" || a.key === "Delete") e[b].value ? e[b].value = "" : b > 0 && e[b - 1].focus();
			else if (a.key === "ArrowLeft" && b > 0) e[b - 1].focus();
			else if (a.key === "ArrowRight" && b < e.length) e[b +
				1].focus();
			else if (a.key === "Enter" && c) c2pHelper.handleDefaultButton(a, c);
			else if (a.key.length == 1) e[b].value = ""
		});
		e[a].addEventListener("input", function ()
		{
			var a = document.getElementById(d);
			if (a) a.innerHTML = "";
			a = this.dataIndex;
			if (f.test(e[a].value)) e[a].value = "";
			if (a === e.length - 1 && e[a].value) return true;
			e[a].value && e[a + 1].focus()
		});
		e[a].addEventListener("paste", function (a)
		{
			a.stopPropagation();
			a.preventDefault();
			a = a.clipboardData.getData("Text").trim();
			if (f.test(a)) e.forEach((a) =>
			{
				return a.value = ""
			});
			else
			{
				a = a.split("");
				let b = 0;
				for (let c = this.dataIndex; c < Math.min(this.dataIndex + a.length, e.length); c++)
				{
					e[c].value = a[b];
					b++;
					e[c].focus()
				}
			}
		})
	}
};
const c2pSdk = {
	init: async function (a)
	{
		return new Promise((b, c) =>
		{
			try
			{
				this.initResolve = b;
				this.initReject = c;
				this.c2pCallbacks = {
					success: a.onPaymentSuccess,
					cancel: a.onPaymentCancel,
					error: a.onPaymentError,
					validate: a.onPaymentValidate,
					prefill: a.onPaymentPrefill
				};
				window.onVisaCheckoutReady = this.initVisaSdk;
				c2pHelper.buildComponent("script", document.body,
				{
					id: "visaSdkScript",
					src: c2pCfg.sdkSettings.urls[c2pCfg.environment],
					type: "text/javascript"
				})
			}
			catch (d)
			{
				logError("init failed", d);
				c(
				{
					message: "init failed",
					error: d
				})
			}
		})
	},
	getInitParameters: async function ()
	{
		return {
			apikey: c2pCfg.apiKey,
			encryptionKey: c2pCfg.encryptionKey,
			externalClientId: c2pCfg.externalClientId,
			settings: Object.assign(
			{}, c2pCfg.sdkSettings.settings),
			paymentRequest: await this.getPaymentRequest()
		}
	},
	getPaymentRequest: async function ()
	{
		if (!c2pHelper.isFunction(this.c2pCallbacks.prefill)) throw "onPaymentPrefill is required";
		const a = await Promise.resolve(this.c2pCallbacks.prefill());
		if (!a) throw "paymentRequest is required";
		return a
	},
	initVisaSdk: async function (a)
	{
		try
		{
			const b =
				await c2pSdk.getInitParameters.call(c2pSdk),
				c = await (a ? window.V.init : window.V.initializeVsb)(b);
			c2pCfg.logDebug("initializeVsb result", JSON.stringify(c));
			c2pCfg.logDebug("Visa V:", JSON.stringify(V));
			if (!(a || c && c.isInitCompleted)) throw {
				message: "initVisaSdk failed",
				result: c
			};
			if (c2pSdk.initResolve)
			{
				window.V.on("payment.success", (a) =>
				{
					c2pSdk.handleCallback.call(c2pSdk, "success", a)
				});
				window.V.on("payment.cancel", (a) =>
				{
					c2pSdk.handleCallback.call(c2pSdk, "cancel", a)
				});
				window.V.on("payment.error", (a, b) =>
				{
					c2pSdk.handleCallback.call(c2pSdk,
						"error", a, b)
				});
				window.V.on("pre-payment.user-data-prefill", () =>
				{
					c2pSdk.invokeCallback(c2pSdk.c2pCallbacks.prefill)
				});
				c2pSdk.initResolve()
			}
			c2pCfg.logDebug("Initialization succeeded")
		}
		catch (b)
		{
			logError("initVisaSdk failed", b);
			c2pSdk.initReject && c2pSdk.initReject(b)
		}
	},
	clearInitResponse: function ()
	{
		this.initReject = this.initResolve = void 0
	},
	handleCallback: function (a, b, c)
	{
		try
		{
			a === "error" ? console.error("payment.error", JSON.stringify(b), JSON.stringify(c)) : c2pCfg.logDebug("payment." + a, JSON.stringify(b));
			if (!b) throw "Invalid request";
			Array.isArray(b) && (b = b[0]);
			const d = b.dpaCheckoutResponse || b,
				e = d.vInitRequest.paymentRequest,
				f = {
					status: a,
					amount: e.total,
					merchantRequestId: e.merchantRequestId,
					merchantOrderId: e.orderId,
					payload:
					{
						transactionId: d.callid,
						externalClientId: d.vInitRequest.externalClientId,
						encryptionKey: d.encKey,
						token: d.encPaymentData
					}
				};
			if (d.why) f.reason = d.why;
			if (c) f.error = c;
			this.invokeCallback(this.c2pCallbacks[a], f)
		}
		catch (d)
		{
			logError("Callback failed", d)
		}
	},
	invokeCallback: function (a, ...b)
	{
		c2pCfg.logDebug("args", b);
		typeof a ===
			"function" && a.apply(a, b)
	},
	getRecognized: async function ()
	{
		try
		{
			const a = await window.V.isRecognized();
			c2pCfg.logDebug("IsRecoginized Response", JSON.stringify(a));
			this.idTokens = a.idTokens;
			this.isRecognized = a.recognized;
			this.usedDevice = a.usedDevice;
			c2pCfg.logDebug("IsRecoginized: ", this.isRecognized);
			return {
				isRecognized: this.isRecognized,
				usedDevice: this.usedDevice
			}
		}
		catch (a)
		{
			logError("isRecognized failed", a);
			return {
				isRecognized: false,
				usedDevice: false
			}
		}
	},
	validateConsumer: async function (a)
	{
		try
		{
			const b = await window.V.identityLookup(a);
			c2pCfg.logDebug("identityLookup", JSON.stringify(b));
			if (!b || !b.consumerPresent)
			{
				c2pCfg.logDebug("validateConsumer failed", b);
				throw b;
			}
			const c = await window.V.initiateIdentityValidation();
			c2pCfg.logDebug("initiateIdentityValidation", JSON.stringify(c));
			return c
		}
		catch (b)
		{
			logError("Error validating Customer", b);
			return b
		}
	},
	validateOtp: async function (a)
	{
		try
		{
			const b = await window.V.completeIdentityValidation(
			{
				validationData: a
			});
			c2pCfg.logDebug("validateOtp", JSON.stringify(b));
			if (!b || !b.isValidationComplete) throw "Consumer validation failed";
			return true
		}
		catch (b)
		{
			logError("Error validating otp", b);
			throw b;
		}
	},
	getProfiles: async function ()
	{
		try
		{
			const a = await window.V.getSrcProfile();
			this.maskedCards = a.maskedCards;
			this.profiles = a.profiles;
			return {
				profiles: this.profiles,
				maskedCards: this.maskedCards
			}
		}
		catch (a)
		{
			logError("Error getting profiles", a);
			return null
		}
	},
	checkout: async function (a)
	{
		try
		{
			{
				const b = await window.V.checkout(
				{
					srcDigitalCardId: a.cardId,
					consumer: a.consumer,
					initializeVsbRequest: a.initParameters
				});
				c2pCfg.logDebug("checkout response",
					JSON.stringify(b));
				return b && b.dcfActionCode ?
				{
					action: b.dcfActionCode
				} :
				{
					action: "NONE"
				}
			}
		}
		catch (b)
		{
			logError("Error while doing checkout", b)
		}
	},
	removeProfile: async function ()
	{
		try
		{
			const a = await window.V.unbindAppInstance();
			c2pHelper.clear();
			c2pCfg.logDebug("removeProfile", JSON.stringify(a, null, 2));
			return true
		}
		catch (a)
		{
			logError("removeProfile", a);
			return false
		}
	},
	uuid4: function ()
	{
		return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (a)
		{
			var b = Math.random() * 16 | 0;
			return (a == "x" ? b : b & 3 | 8).toString(16)
		})
	}
};
const click2pay = {
	init: function (a)
	{
		setTimeout(async () =>
		{
			let b = null,
				c = null;
			try
			{
				a.environment = c2pCfg.convertEnvironment(a.environment);
				click2pay.validateInit(a);
				c2pCfg.logDebug("init:", a.environment);
				c2pCfg.setConfiguration(a);
				b = await c2pHelper.buildWaitScreen(c2pCfg.click2payContainer);
				let d = await c2pSdk.init(a);
				d = await click2pay.initFlow.call(click2pay, false);
				c2pCfg.logDebug(d);
				c = {
					status: iStatus.success
				}
			}
			catch (d)
			{
				logError("click2pay.init error: ", d);
				c = {
					status: iStatus.error,
					reason: exMsg(d, "Failed to load Click-To-Pay")
				};
				throw d;
			}
			finally
			{
				c2pSdk.clearInitResponse();
				if (a.onButtonLoaded) try
				{
					a.onButtonLoaded(c)
				}
				catch (d)
				{
					logError("click2pay.onButtonLoaded error: ", d)
				}
				b && c2pHelper.removeComponent(b)
			}
		}, 1)
	},
	validateInit: function (a)
	{
		if (!a) throw "initArgs is required";
		if (isDefined(a.environment) && a.environment !== "")
		{
			if (typeof a.environment !== "number") throw "initArgs.environment must be a valid c2pEnvironment value";
			if (a.environment < 0 || a.environment >= c2pCfg.sdkSettings.urls.length) throw "initArgs.environment is out of range. It must be a valid c2pEnvironment value";
		}
		else throw "initArgs.environment is required. Must be a valid c2pEnvironment value";
		if (!a.externalClientId) throw "initArgs.externalClientId is required";
		if (!a.click2payContainer) throw "initArgs.click2payContainer is required";
		a.click2payContainer = document.getElementById(a.click2payContainer);
		if (!a.click2payContainer) throw "initArgs.click2payContainer must be a valid ID of <div> element";
		if (a.cardContainerType && !c2pCardContainerType.isValid(a.cardContainerType)) throw "initArgs.cardContainerType must be a valid c2pCardContainerType value";
		if (!a.onPaymentPrefill) throw "'onPaymentPrefill' is required";
		if (!c2pHelper.isFunction(a.onPaymentPrefill)) throw "'onPaymentPrefill' must be a function";
		if (a.onButtonLoaded && !c2pHelper.isFunction(a.onButtonLoaded)) throw "'onButtonLoaded' must be a function";
		if (a.onPaymentValidate && !c2pHelper.isFunction(a.onPaymentValidate)) throw "'onPaymentValidate' must be a function";
		if (a.onPaymentSuccess && !c2pHelper.isFunction(a.onPaymentSuccess)) throw "'onPaymentSuccess' must be a function";
		if (a.onPaymentCancel && !c2pHelper.isFunction(a.onPaymentCancel)) throw "'onPaymentCancel' must be a function";
		if (a.onPaymentError && !c2pHelper.isFunction(a.onPaymentError)) throw "'onPaymentError' must be a function";
		if (a.environment === c2pEnvironment.production && !a.externalClientId) throw "initArgs.externalClientId is required.";
	},
	initFlow: async function ()
	{
		return new Promise((a, b) =>
		{
			const c = async () =>
			{
				let a = {};
				c2pCfg.forceClickToPayButton || (a = await c2pSdk.getRecognized());
				this.mainDiv = a.isRecognized ? await this.displayProfiles() : a.usedDevice ? this.buildSignInScreen() : this.displayNewUser()
			};
			try
			{
				let d = 0;
				const e = setInterval(async () =>
				{
					try
					{
						d++;
						c2pCfg.logDebug("Loading SDK Counter: " + d);
						if (d === 150)
						{
							clearInterval(e);
							logError("Click-To-Pay Timed Out")
						}
						if (window.V)
						{
							clearInterval(e);
							const b = await c();
							a(b)
						}
					}
					catch (f)
					{
						logError("click2pay.initFlow error: ", f);
						b(f)
					}
				}, 100)
			}
			catch (d)
			{
				logError("click2pay.initFlow error: ", d);
				b(d)
			}
		})
	},
	buildSignInScreen: function ()
	{
		const a = c2pHelper.buildComponent("div", c2pCfg.click2payContainer,
		{
			id: "divSignIn",
			class: c2pCfg.mainCssClass
		});
		c2pHelper.buildSignInScreen(a, this.validateConsumer);
		return a
	},
	validatePayment: async function (a)
	{
		try
		{
			c2pCfg.logDebug("validate payment:",
				a);
			if (a.button === 0)
			{
				if (c2pHelper.isFunction(c2pSdk.c2pCallbacks.validate))
				{
					const a = await Promise.resolve(c2pSdk.c2pCallbacks.validate());
					c2pCfg.logDebug(a)
				}
				if (c2pHelper.isFunction(c2pSdk.c2pCallbacks.prefill))
				{
					const a = await Promise.resolve(c2pSdk.c2pCallbacks.prefill());
					if (a)
					{
						const b = await c2pSdk.initVisaSdk(true, a);
						c2pCfg.logDebug(b)
					}
				}
			}
		}
		catch (b)
		{
			logError("validatePayment", b);
			a.preventDefault();
			a.stopPropagation()
		}
	},
	validateConsumer: async function (a)
	{
		const b = await c2pSdk.validateConsumer(a);
		c2pHelper.removeComponent(click2pay.mainDiv);
		if (b && b.maskedValidationChannel)
		{
			click2pay.mainDiv = c2pHelper.buildComponent("div", c2pCfg.click2payContainer,
			{
				id: "divOtp",
				class: c2pCfg.mainCssClass
			});
			c2pHelper.buildOtpScreen(click2pay.mainDiv, click2pay.validateOtp, a, c2pSdk.validateConsumer)
		}
		else click2pay.mainDiv = await click2pay.displayNewUser()
	},
	validateOtp: async function (a, b)
	{
		try
		{
			if (await c2pSdk.validateOtp(a)) click2pay.mainDiv = await click2pay.displayProfiles.call(click2pay)
		}
		catch (c)
		{
			if (b >= 3) setTimeout(() =>
			{
				c2pHelper.removeComponent(click2pay.mainDiv);
				click2pay.mainDiv = click2pay.buildSignInScreen()
			}, 10);
			else throw c;
		}
	},
	displayNewUser: async function ()
	{
		const a = c2pHelper.buildComponent("div", c2pCfg.click2payContainer,
		{
			id: "divNewUser",
			class: `${c2pCfg.mainCssClass} datatooltip`,
			style: "display:none"
		});
		var b = a.addComponent("div",
		{
			class: "c2p-new-btn"
		});
		b.addEventListener("click", (a) =>
		{
			click2pay.validatePayment(a).catch((a) =>
			{
				return logError("validatePayment error", a)
			})
		}, true);
		b.addComponent("img",
		{
			id: "c2pBtn",
			src: c2pCfg.c2pBtnImg,
			class: "v-button c2p-new-btn",
			role: "button",
			alt: "Click-To-Pay"
		});
		b = await c2pSdk.initVisaSdk(true);
		c2pCfg.logDebug(b);
		a.style.display = "";
		return a
	},
	displayProfiles: async function ()
	{
		const a = await c2pSdk.getProfiles();
		c2pCfg.logDebug("displayProfiles - profileInfo:", JSON.stringify(a));
		const b = c2pHelper.buildComponent("div", c2pCfg.click2payContainer,
		{
			id: "divUserProfiles",
			class: c2pCfg.mainCssClass,
			style: "display:none"
		});
		let c = null;
		a && a.maskedCards && (c = c2pHelper.buildProfilesScreen(a, b, this.checkout));
		c || c2pHelper.buildNoProfilesScreen(b);
		c2pHelper.removeComponent(click2pay.mainDiv);
		b.style = "";
		return b
	},
	resetScreen: function (a, b)
	{
		c2pCfg.logDebug("resetScreen - mainDiv", this.mainDiv ? this.mainDiv.id : "null");
		c2pHelper.removeComponent(this.mainDiv);
		setTimeout(async () =>
		{
			let c = null;
			try
			{
				b && (c = await c2pHelper.buildWaitScreen(c2pCfg.click2payContainer));
				if (a) this.mainDiv = this.buildSignInScreen();
				else
				{
					const a = await this.initFlow();
					c2pCfg.logDebug(a)
				}
			}
			finally
			{
				c && c2pHelper.removeComponent(c)
			}
		}, 1)
	},
	checkout: async function (a)
	{
		if (c2pHelper.isFunction(c2pSdk.c2pCallbacks.validate))
		{
			const a =
				await Promise.resolve(c2pSdk.c2pCallbacks.validate());
			c2pCfg.logDebug("checkout-validate", a)
		}
		a.initParameters = c2pSdk.getInitParameters();
		try
		{
			const b = await c2pSdk.checkout(a);
			c2pCfg.logDebug(b)
		}
		finally
		{
			click2pay.resetScreen.call(click2pay, false, c2pCfg.displayWaitScreenAfterCheckout)
		}
	}
};