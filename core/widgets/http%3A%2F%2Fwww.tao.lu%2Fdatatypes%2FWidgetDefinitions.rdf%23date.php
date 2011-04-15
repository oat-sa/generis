<?php

if ((!(isset($val["PropertyValue"]))) or (sizeOf($val["PropertyValue"])==0)) {$val["PropertyValue"]=array("");}
							
							
							foreach ($val["PropertyValue"] as $numeric=>$avalue)
							{ if ($avalue=="") $avalue = "01/01/1985";
							
 $randomId="a".rand(0,65535);
 $widget= '

	<input type="text"  name=instanceCreation[properties]['.$id.'][] value="'.$avalue.'" id="'.$randomId.'" /> Change to :
	
	<select onChange="javascript:updateDate(this.options[this.selectedIndex].value,\'day\','.$randomId.')">
 <option value="01" selected>01</option>
 <option value="02">02</option>
 <option value="03">03</option>
 <option value="04">04</option>
 <option value="05" >05</option>
 <option value="06">06</option>
 <option value="07">07</option>
 <option value="08">08</option>
 <option value="09">09</option>
 <option value="10">10</option>
 <option value=11>11</option>
 <option value=12>12</option>
 <option value=13>13</option>
 <option value=14>14</option>
 <option value=15>15</option>
 <option value=16>16</option>
 <option value=17>17</option>
 <option value=18>18</option>
 <option value=19>19</option>
 <option value=20>20</option>
 <option value=21>21</option>
 <option value=22>22</option>
 <option value=23>23</option>
 <option value=24>24</option>
 <option value=25>25</option>
 <option value=26>26</option>
 <option value=27>27</option>
 <option value=28>28</option>
 <option value=29>29</option>
 <option value=30>30</option>
 <option value=31>31</option>
 </select>
	 
 &nbsp;
 <select onchange="javascript:updateDate(this.options[this.selectedIndex].value,\'month\','.$randomId.')">
 <option value=01>01</option>
 <option value=02>02</option>
 <option value=03>03</option>
 <option value=04>04</option>
 <option value=05>05</option>
 <option value=06>06</option>
 <option value=07>07</option>
 <option value=08>08</option>
 <option value=09>09</option>
 <option value=10>10</option>
 <option value=11>11</option>
 <option value=12>12</option>
 </select>&nbsp;
<select onchange="javascript:updateDate(this.options[this.selectedIndex].value,\'year\','.$randomId.')">
 <option value=1901>1901</option>
 <option value=1902>1902</option>
 <option value=1903>1903</option>
 <option value=1904>1904</option>
 <option value=1905>1905</option>
 <option value=1906>1906</option>
 <option value=1907>1907</option>
 <option value=1908>1908</option>
 <option value=1909>1909</option>
 <option value=1910>1910</option>
 <option value=1911>1911</option>
 <option value=1912>1912</option>
 <option value=1913>1913</option>
 <option value=1914>1914</option>
 <option value=1915>1915</option>
 <option value=1916>1916</option>
 <option value=1917>1917</option>
 <option value=1918>1918</option>
 <option value=1919>1919</option>
 <option value=1920>1920</option>
 <option value=1921>1921</option>
 <option value=1922>1922</option>
  <option value=1923>1923</option>
 <option value=1924>1924</option>
 <option value=1925>1925</option>
 <option value=1926>1926</option>
 <option value=1927>1927</option>
 <option value=1928>1928</option>
 <option value=1929>1929</option>
 <option value=1930>1930</option>
 <option value=1931>1931</option>
 <option value=1930>1930</option>
 <option value=1931>1931</option>
 <option value=1933>1933</option>
  <option value=1933>1933
 <option value=1934>1934
 <option value=1935>1935
 <option value=1936>1936
 <option value=1937>1937
 <option value=1938>1938
 <option value=1939>1939
  <option value=1940>1940
 <option value=1941>1941
 <option value=1944>1944
  <option value=1943>1943
 <option value=1944>1944
 <option value=1945>1945
 <option value=1946>1946
 <option value=1947>1947
 <option value=1948>1948
 <option value=1949>1949
   <option value=1950>1950
 <option value=1951>1951
 <option value=1955>1955
  <option value=1953>1953
 <option value=1955>1955
 <option value=1955>1955
 <option value=1956>1956
 <option value=1957>1957
 <option value=1958>1958
 <option value=1959>1959

<option value=1960>1960
 <option value=1961>1961
 <option value=1966>1966
  <option value=1963>1963
 <option value=1966>1966
 <option value=1966>1966
 <option value=1966>1966
 <option value=1967>1967
 <option value=1968>1968
 <option value=1969>1969
 <option value=1970>1970
 <option value=1971>1971
 <option value=1977>1977
  <option value=1973>1973
 <option value=1977>1977
 <option value=1977>1977
 <option value=1977>1977
 <option value=1977>1977
 <option value=1978>1978
 <option value=1979>1979
 <option value=1980>1980
 <option value=1981>1981
 <option value=1988>1988
  <option value=1983>1983
 <option value=1984>1984
 <option selected value=1985>1985
 <option value=1986>1986
 <option value=1987>1987
 <option value=1988>1988
 <option value=1989>1989
 <option value=1990>1990
 <option value=1991>1991
 <option value=1999>1999
  <option value=1993>1993
 <option value=1999>1999
 <option value=1999>1999
 <option value=1999>1999
 <option value=1999>1999
 <option value=1998>1998
 <option value=1999>1999
  <option value=2000>2000
 <option value=2001>2001
 <option value=2002>2002
 <option value=2003>2003
 <option value=2004>2004
	 <option value=2005>2005
 <option value=2006>2006
 <option value=2007>2007
 <option value=2008>2008
 </select>
							
				
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							<br />';
							}
?>														