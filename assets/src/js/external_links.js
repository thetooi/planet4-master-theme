export const setupExternalLinks = function($) {
  let siteURL = window.location.host;

  const inlinedSVG = `<svg class="external-icon" width="284px" height="284px" viewBox="0 0 284 284" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <g fill="currentColor" fill-rule="nonzero">
              <path d="M14.3,283.7 C14.1,283 13.4,283.1 13,282.9 C4.8,279.9 0.5,273.8 0.5,265 C0.5,190.1 0.5,115.2 0.5,40.3 C0.5,33 0.5,25.7 0.5,18.4 C0.5,7.9 8.2,0.2 18.6,0.2 C56.5,0.2 94.4,0.2 132.4,0.2 C138.7,0.2 142.2,3.7 142.2,9.9 C142.2,15.2 142.2,20.4 142.2,25.7 C142.2,32.1 138.7,35.6 132.3,35.6 C101.2,35.6 70.1,35.6 39,35.6 C36.6,35.6 35.8,36 35.8,38.6 C35.9,107.3 35.9,176 35.8,244.8 C35.8,247.8 36.6,248.3 39.4,248.3 C108,248.2 176.5,248.2 245.1,248.3 C248.1,248.3 248.6,247.4 248.6,244.7 C248.5,213.9 248.5,183.1 248.5,152.3 C248.5,145.3 251.9,142 258.8,142 C263.3,142 267.9,142.2 272.4,141.9 C277.9,141.5 281.8,143.5 283.9,148.6 C283.9,189 283.9,229.4 283.9,269.8 C282.4,272.4 281.5,275.4 279.4,277.8 C276.5,280.9 272.9,282.5 269,283.7 C184.1,283.7 99.2,283.7 14.3,283.7 Z"></path>
              <path d="M284,100.5 C283.3,100.6 283.3,101.3 283,101.7 C279.7,107.4 273.2,108.3 268.6,103.7 C258.7,93.9 248.9,84.1 239.1,74.1 C237.5,72.4 236.7,72.6 235.1,74.2 C206.7,102.7 178.3,131.1 149.8,159.5 C144.5,164.8 139.9,164.8 134.6,159.5 C131.1,156 127.7,152.6 124.2,149.1 C119.6,144.4 119.6,139.5 124.2,134.9 C152.7,106.4 181.2,77.9 209.7,49.5 C211.6,47.6 211.6,46.8 209.7,45 C200,35.5 190.5,25.9 180.9,16.3 C177.8,13.3 176.5,9.9 178.3,5.8 C180,1.8 183.4,0.3 187.7,0.4 C216,0.5 244.3,0.5 272.6,0.4 C278,0.4 281.7,2.1 283.9,7.1 C284,38.1 284,69.3 284,100.5 Z"></path>
          </g>
      </g>
  </svg>`;

  $('div.page-template a:not(.btn):not(.cover-card-heading), article a:not(.btn):not(.cover-card-heading)').each(function () {
    let href = undefined === $(this).attr('href') ? '' : $(this).attr('href');
    if (href != '' && href.indexOf(siteURL) <= -1 && href.length > 0) {
      if ($(this).text().trim().length == 0) {
        return;
      }

      if (href.charAt(0) != '/' && href.charAt(0) != '#' && !href.endsWith('.pdf') && !href.startsWith('javascript:')) {
        $(this).append(inlinedSVG);
        $(this).attr('target', '_blank');
        $(this).addClass('external-link');
      }
    }
  });
};
