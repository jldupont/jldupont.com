/**
 * Uses the syntax highlighter found
 * at http://code.google.com/p/syntaxhighlighter/
 * 
 * Usage:
 * 
 * <textarea name="code" class="js" rows="20" cols="70">
 *   // place JS code here
 * </textarea>
 * 
 * <script language="javascript" src="http://jeanlou.dupont.googlepages.com/shCore.js"></script>
 * <script class="javascript" src="http://jeanlou.dupont.googlepages.com/shBrushJScript.js"></script>
 * <script class="javascript" src="http://jeanlou.dupont.googlepages.com/dp.js"></script>
 * 
 * @author Jean-Lou Dupont
 * @version 0.1
 */
dp.SyntaxHighlighter.BloggerMode();
dp.SyntaxHighlighter.ClipboardSwf = 'http://jeanlou.dupont.googlepages.com/clipboard.swf';
dp.SyntaxHighlighter.HighlightAll('code');
document.write('<link type="text/css" rel="stylesheet" href="http://jeanlou.dupont.googlepages.com/SyntaxHighlighter.css">');
